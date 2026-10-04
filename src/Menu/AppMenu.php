<?php

namespace App\Menu;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Controller\CongressController;
use App\Entity\Instrument;
use App\Entity\Jeopardy;
use App\Entity\Official;
use Survos\FieldBundle\Registry\EntityMetaRegistry;
use Survos\TablerBundle\Event\MenuEvent;
use Survos\TablerBundle\Service\ContextService;
use Survos\TablerBundle\Menu\MenuBuilderTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AppMenu
{
    use MenuBuilderTrait;

    private const ES_BROWSER_DEMOS = [
        'app_movie' => 'movie',
        'app_car' => 'car',
        'app_marvel' => 'marvel',
        'app_wcma' => 'wcma',
    ];

    public function __construct(
        private ContextService                 $contextService,
        #[Autowire('%kernel.environment%')] protected string $env,
        private IriConverterInterface $iriConverter,
        private EntityMetaRegistry $entityRegistry,
        private ?AuthorizationCheckerInterface $security = null,
    )
    {
    }

    #[AsEventListener(event: MenuEvent::NAVBAR_MENU)]
    public function midNavbarMenu(MenuEvent $event): void
    {
        $menu = $event->getMenu();
        foreach (['app_homepage'] as $route)
        {
            $this->add($menu, $route, translationDomain: 'routing', label: $route); // label: u($route)->after('app_')
        }
        $this->add($menu, 'survos_workflow_entities', label: "*entities", translationDomain: 'routing');
        // Per-entity dropdowns (#[EntityMeta]-annotated classes): every way we have
        // to search/browse this entity's data. InstantSearch only appears for the
        // Elasticsearch browser demos; ux-search and Doctrine-api-grid are generic
        // and always shown. The old
        // single link to the EntityDashboardController "everything" page is now
        // the last item, not the entry point.
        foreach ($this->entityRegistry->getBrowsable() as $descriptor) {
            $submenu = $this->addSubmenu($menu, $descriptor->label, icon: $descriptor->icon, translationDomain: 'routing');

            if (isset(self::ES_BROWSER_DEMOS[$descriptor->code])) {
                $this->add($submenu, 'bench_search', ['code' => self::ES_BROWSER_DEMOS[$descriptor->code]], label: 'InstantSearch (Elasticsearch)', translationDomain: 'routing');
            }


            $this->add($submenu, 'survos_admin_browse', ['code' => $descriptor->code], label: 'Doctrine search (api-grid)', translationDomain: 'routing');


            $this->add($submenu, 'survos_entity_ux_search', ['code' => $descriptor->code], label: 'Search (ux-search)', dividerAfter: true, translationDomain: 'routing');

            if (null !== $doctrineCollectionUrl = $this->doctrineCollectionUrl($descriptor->class)) {
                $this->add($submenu, uri: $doctrineCollectionUrl, label: 'raw GetCollection (Doctrine)', external: true, icon: 'mdi:code-json', dividerAfter: true, translationDomain: 'routing');
            }

            $this->add($submenu, 'survos_entity_dashboard', ['code' => $descriptor->code], label: 'Overview', translationDomain: 'routing');
        }

        if ($this->env === 'dev') {
            $this->add($menu, 'survos_commands', label: "Commands", translationDomain: 'routing');
        }
        $submenu = $this->addSubmenu($menu, 'Flysystem', translationDomain: 'routing');
        foreach (['flysystem_browse_default'] as $route) {
            $this->add($submenu, $route, translationDomain: 'routing', label: $route);
        }

        foreach ($this->contextService->getConfig()['app']['social'] ?? [] as $platform => $value) {
            $this->add($menu, uri: $value, label: $platform, external: true, icon: 'bi:' . $platform, translationDomain: 'routing');
        }

//        foreach (['app_credit'] as $route) {
//        }
        }

    private function doctrineCollectionUrl(string $class): ?string
    {
        try {
            return $this->iriConverter->getIriFromResource($class, operation: new GetCollection());
        } catch (\Throwable) {
            return null;
        }
    }

    public function lastNavbarMenu(MenuEvent $event): void
    {
        return;
//        <li class="nav-item">
//                        <a target="_blank" rel="noopener" class="nav-link"
//                           href="https://github.com/thomaspark/bootswatch/"><i class="bi bi-github"></i><span
//                                    class="d-lg-none ms-2">GitHub</span></a>
//                    </li>
//                    <li class="nav-item">
//                        <a target="_blank" rel="noopener" class="nav-link" href="https://twitter.com/bootswatch"><i
//                                    class="bi bi-twitter"></i><span class="d-lg-none ms-2">Twitter</span></a>
//                    </li>
//

        $menu = $event->getMenu();
        foreach ($this->contextService->getConfig()['app']['social'] ?? [] as $platform => $value) {
            $this->add($menu, uri: $value, label: $platform, external: true, icon: 'bi:' . $platform, translationDomain: 'routing');
        }
        $this->addDivider($menu);

        if (0) {
            $nested = $this->addSubmenu($menu, 'github', icon: 'bi:github', translationDomain: 'routing');
            $this->add($nested, label: 'repo', uri: $this->contextService->getConfig()['app']['social']['github'], translationDomain: 'routing');
            $this->add($nested, label: 'issues', uri: $this->contextService->getConfig()['app']['social']['github'] . '/issues', translationDomain: 'routing');
        }
    }

    private function isDev(): bool
    {
        return $this->env === 'dev';
    }

    public function startNavbarMenu(MenuEvent $event): void
    {
        return;
        $menu = $event->getMenu();


        $this->add($menu, 'app_homepage', label: "Home", translationDomain: 'routing');

        $this->add($menu, 'api_doc', label: 'API', external: true, translationDomain: 'routing');

        foreach ([CongressController::class,
//                     TermCrudController::class
                 ] as $controllerClass) {
            $controllerMenu = $this->addSubmenu($menu, label: (new \ReflectionClass($controllerClass))->getShortName(), translationDomain: 'routing');
            foreach (['simple_datatables',
//                         'index',
//                         'crud_index'
                     ] as $controllerRoute) {
                $this->add($menu, $controllerClass . '::' . $controllerRoute, label: $controllerRoute, translationDomain: 'routing');
            }

        }
    }

    public function pageMenu(MenuEvent $event): void
    {
    }

    #[AsEventListener(event: MenuEvent::FOOTER)]
    public function footerMenu(MenuEvent $event): void
    {
        $menu = $event->getMenu();

        foreach (['app_homepage'] as $route) {
            $this->add($menu, $route, translationDomain: 'routing', label: $route);
        }
        return;
        $nestedMenu = $this->addSubmenu($menu, 'Credits', translationDomain: 'routing');
        foreach (['bundles', 'javascript'] as $type) {
            $this->add($nestedMenu, uri: "#$type", label: ucfirst($type), translationDomain: 'routing');
        }

    }

    public function sidebarMenu(MenuEvent $event): void
    {
    }
}
