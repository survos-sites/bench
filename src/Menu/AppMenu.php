<?php

declare(strict_types=1);

namespace App\Menu;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use App\Controller\CongressController;
use App\Entity\Instrument;
use App\Entity\Jeopardy;
use App\Entity\Official;
use Survos\FieldBundle\Registry\EntityMetaRegistry;
use Survos\MeiliBundle\Registry\MeiliRegistry;
use Survos\TablerBundle\Event\MenuEvent;
use Survos\TablerBundle\Service\ContextService;
use Survos\TablerBundle\Menu\MenuBuilderTrait;
use Survos\MeiliBundle\Service\MeiliService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Survos\MenuBundle\Attribute\AsMenuListener;
use Survos\MenuBundle\MenuSlot;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AppMenu
{
    use MenuBuilderTrait;

    public function __construct(
        private ContextService                 $contextService,
        #[Autowire('%kernel.environment%')] protected string $env,
        private MeiliService $meiliService,
        private MeiliRegistry $meiliRegistry,
        private IriConverterInterface $iriConverter,
        private EntityMetaRegistry $entityRegistry,
        private \App\Search\EntityDuckDbLinks $duckDbLinks,
        private ?AuthorizationCheckerInterface $security = null,
    )
    {
    }

    #[AsMenuListener(MenuSlot::NAVBAR_MENU)]
    public function midNavbarMenu(MenuEvent $event): void
    {
        $menu = $event->getMenu();
        $this->add($menu, 'local_product_search', label: 'Products');
        $this->add($menu, 'movie_ask', label: 'Ask movies');
        $this->add($menu, 'movie_browse', label: 'Browse movies');
        $this->add($menu, 'demo_product_duckdb', label: 'DuckDB products');
        foreach (['app_homepage', 'meili_admin'] as $route)
        {
            $this->add($menu, $route); // label: u($route)->after('app_')
        }
        $this->add($menu, 'survos_workflow_extras', ['flowCode' => \App\Workflow\OfficialWorkflow::WORKFLOW_NAME], label: 'Official workflow');
        // Per-entity dropdowns (#[EntityMeta]-annotated classes): every way we have
        // to search/browse this entity's data, from fastest-to-set-up to
        // richest-but-heaviest. InstantSearch only appears when the
        // class actually has that capability (MeiliIndex);
        // ux-search and Doctrine-api-grid are generic and always shown. The old
        // single link to the EntityDashboardController "everything" page is now
        // the last item, not the entry point.
        foreach ($this->entityRegistry->getBrowsable() as $descriptor) {
            $submenu = $this->addSubmenu($menu, $descriptor->label, icon: $descriptor->icon);

            if (null !== $meiliBaseName = $this->meiliBaseNameFor($descriptor->class)) {
                $this->add($submenu, 'meili_insta', ['indexName' => $this->meiliRegistry->uidFor($meiliBaseName)], label: 'InstantSearch (Meilisearch)');
            }

            $this->add($submenu, 'survos_admin_browse', ['code' => $descriptor->code], label: 'Doctrine search (api-grid)');

            $this->add($submenu, 'survos_entity_ux_search', ['code' => $descriptor->code], label: 'Search (ux-search / Doctrine LIKE)', dividerAfter: true);

            if (null !== $duckDb = $this->duckDbLinks->forEntity($descriptor->code)) {
                $this->add($submenu, $duckDb['route'], $duckDb['parameters'], label: 'DuckDB search', icon: 'tabler:database-search');
            }

            if (null !== $doctrineCollectionUrl = $this->doctrineCollectionUrl($descriptor->class)) {
                $this->add($submenu, uri: $doctrineCollectionUrl, label: 'raw GetCollection (Doctrine)', external: true, icon: 'mdi:code-json', dividerAfter: true);
            }

            $this->add($submenu, 'survos_entity_dashboard', ['code' => $descriptor->code], label: 'Overview');
        }

        if ($this->env === 'dev') {
            $this->add($menu, 'survos_commands', label: "Commands");
        }
        $submenu = $this->addSubmenu($menu, 'Flysystem');
        foreach (['flysystem_browse_default'] as $route) {
            $this->add($submenu, $route);
        }

        foreach ($this->contextService->getConfig()['app']['social'] ?? [] as $platform => $value) {
            $this->add($menu, uri: $value, label: $platform, external: true, icon: 'bi:' . $platform);
        }

//        foreach (['app_credit'] as $route) {
//            $this->add($menu, $route, label: u($route)->after('app_'));
//        }
        }

    private function meiliBaseNameFor(string $class): ?string
    {
        foreach ($this->meiliRegistry->names() as $baseName) {
            if ($this->meiliRegistry->classFor($baseName) === $class) {
                return $baseName;
            }
        }

        return null;
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
            $this->add($menu, uri: $value, label: $platform, external: true, icon: 'bi:' . $platform);
        }
        $this->add($menu, label: ' ', dividerAfter: true);

        if (0) {
            $nested = $this->addSubmenu($menu, 'github', icon: 'bi:github');
            $this->add($nested, label: 'repo', uri: $this->contextService->getConfig()['app']['social']['github']);
            $this->add($nested, label: 'issues', uri: $this->contextService->getConfig()['app']['social']['github'] . '/issues');
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


        $this->add($menu, 'app_homepage', label: "Home");
        // app_simple?

        $this->add($menu, 'api_doc', label: 'API', external: true);
        // for nested menus, don't add a route, just a label, then use it for the argument to addMenuItem

        foreach ([CongressController::class,
//                     TermCrudController::class
                 ] as $controllerClass) {
            $controllerMenu = $this->addSubmenu($menu,
                label: (new \ReflectionClass($controllerClass))->getShortName());
            foreach (['simple_datatables',
//                         'index',
//                         'crud_index'
                     ] as $controllerRoute) {
                $this->add($menu, $controllerClass . '::' . $controllerRoute,
                    label: $controllerRoute);
            }

        }
    }

    public function pageMenu(MenuEvent $event): void
    {
    }

    #[AsMenuListener(MenuSlot::FOOTER)]
    public function footerMenu(MenuEvent $event): void
    {
        $menu = $event->getMenu();

        foreach (['app_homepage'] as $route) {
            $this->add($menu, $route);
        }
        return;
        $nestedMenu = $this->addSubmenu($menu, 'Credits');
        foreach (['bundles', 'javascript'] as $type) {
            // $this->addMenuItem($nestedMenu, ['route' => 'survos_base_credits', 'rp' => ['type' => $type], 'label' => ucfirst($type)]);
            $this->addMenuItem($nestedMenu, ['uri' => "#$type", 'label' => ucfirst($type)]);
        }

    }

    public function sidebarMenu(MenuEvent $event): void
    {
    }
}
