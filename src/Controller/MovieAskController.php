<?php

declare(strict_types=1);

namespace App\Controller;

use App\Search\MovieElasticCatalog;
use League\CommonMark\CommonMarkConverter;
use Psr\Log\LoggerInterface;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieAskController extends AbstractController
{
    private const SESSION_KEY = 'movie_snapshot_chat';

    public function __construct(
        #[Autowire(service: 'ai.agent.movie_snapshot')] private readonly AgentInterface $agent,
        private readonly MovieElasticCatalog $catalog,
        private readonly LoggerInterface $logger,
    ) {}

    #[Route('/ask/movies', name: 'movie_ask', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $turns = $session->get(self::SESSION_KEY, []);
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('movie_ask', $request->request->getString('_token'))) { throw $this->createAccessDeniedException('Invalid CSRF token.'); }
            $question = trim($request->request->getString('question'));
            if ('' === $question || mb_strlen($question) > 1000) { return new Response('Enter a question of 1–1000 characters.', 400); }
            $last = $session->get('movie_ask_last', 0);
            if (time()-$last < 3) { return new Response('Please wait a moment before asking again.', 429); }
            $session->set('movie_ask_last', time());
            $messages = [];
            foreach (array_slice($turns, -4) as $turn) {
                $messages[] = Message::ofUser($turn['question']);
                $messages[] = Message::ofAssistant($turn['answer']);
            }
            $messages[] = Message::ofUser($question);
            $this->catalog->reset();
            $start = microtime(true);
            try {
                $answer = trim((string) $this->agent->call(new MessageBag(...$messages))->getContent());
                $failed = false;
            } catch (\Throwable $error) {
                // Do not expose provider errors, credentials, SQL or filesystem paths in the page.
                $this->logger->warning('Movie Ask failed.', ['exception_type'=>$error::class]);
                $answer = 'The movie assistant could not complete this request. Please try again.';
                $failed = true;
            }
            $turns[] = ['question'=>$question, 'answer'=>$answer, 'failed'=>$failed, 'sources'=>$this->catalog->sources(), 'calls'=>$this->catalog->calls(), 'seconds'=>round(microtime(true)-$start, 2)];
            $session->set(self::SESSION_KEY, array_slice($turns, -12));
            return $this->redirectToRoute('movie_ask', ['_fragment'=>'conversation']);
        }
        $messages = [];
        foreach ($turns as $turn) {
            $messages[] = ['role'=>'You', 'text'=>$turn['question']];
            $messages[] = ['role'=>'Movie guide', 'text'=>$turn['answer']];
        }
        $markdown = new CommonMarkConverter(['html_input'=>'escape', 'allow_unsafe_links'=>false]);
        foreach ($turns as &$turn) {
            $turn['html'] = (string) $markdown->convert($turn['answer']);
        }
        unset($turn);
        return $this->render('movies/ask.html.twig', ['messages'=>$messages, 'turns'=>$turns]);
    }

    #[Route('/ask/movies/reset', name: 'movie_ask_reset', methods: ['POST'])]
    public function reset(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('movie_ask_reset', $request->request->getString('_token'))) { throw $this->createAccessDeniedException(); }
        $request->getSession()->remove(self::SESSION_KEY);
        return $this->redirectToRoute('movie_ask');
    }
}
