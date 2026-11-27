<?php
declare(strict_types=1);
namespace App\Command;

use App\Search\MovieSnapshotCatalog;
use Symfony\AI\Agent\AgentInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand('app:movies:ask', 'Ask the fixed movie snapshot and print consulted sources and retrieval timings')]
final readonly class MovieAskCommand
{
    public function __construct(
        #[Autowire(service: 'ai.agent.movie_snapshot')] private AgentInterface $agent,
        private MovieSnapshotCatalog $catalog,
    ) {}

    public function __invoke(#[Argument('Question about the early-2025 movie snapshot')] string $question, SymfonyStyle $io): int
    {
        $this->catalog->reset();
        $start = microtime(true);
        $answer = $this->agent->call($question)->getContent();
        $io->writeln((string) $answer);
        $io->writeln(json_encode(['sources'=>$this->catalog->sources(), 'calls'=>$this->catalog->calls(), 'seconds'=>round(microtime(true)-$start,2)], JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));
        return 0;
    }
}
