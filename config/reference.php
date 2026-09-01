<?php

// This file is auto-generated and is for apps only. Bundles SHOULD NOT rely on its content.

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Config\Loader\ParamConfigurator as Param;

/**
 * This class provides array-shapes for configuring the services and bundles of an application.
 *
 * Services declared with the config() method below are autowired and autoconfigured by default.
 *
 * This is for apps only. Bundles SHOULD NOT use it.
 *
 * Example:
 *
 *     ```php
 *     // config/services.php
 *     namespace Symfony\Component\DependencyInjection\Loader\Configurator;
 *
 *     return App::config([
 *         'services' => [
 *             'App\\' => [
 *                 'resource' => '../src/',
 *             ],
 *         ],
 *     ]);
 *     ```
 *
 * @psalm-type ImportsConfig = list<string|array{
 *     resource: string,
 *     type?: string|null,
 *     ignore_errors?: bool|'not_found',
 * }>
 * @psalm-type ParametersConfig = array<string, scalar|\UnitEnum|array<scalar|\UnitEnum|array<mixed>|Param|null>|Param|null>
 * @psalm-type ArgumentsType = list<mixed>|array<string, mixed>
 * @psalm-type CallType = array<string, ArgumentsType>|array{0:string, 1?:ArgumentsType, 2?:bool}|array{method:string, arguments?:ArgumentsType, returns_clone?:bool}
 * @psalm-type TagsType = list<string|array<string, array<string, mixed>>> // arrays inside the list must have only one element, with the tag name as the key
 * @psalm-type CallbackType = string|array{0:string|ReferenceConfigurator,1:string}|\Closure|ReferenceConfigurator|ExpressionConfigurator
 * @psalm-type DeprecationType = array{package: string, version: string, message?: string}
 * @psalm-type DefaultsType = array{
 *     public?: bool,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 * }
 * @psalm-type InstanceofType = array{
 *     shared?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     properties?: array<string, mixed>,
 *     configurator?: CallbackType,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 * }
 * @psalm-type DefinitionType = array{
 *     class?: string,
 *     file?: string,
 *     parent?: string,
 *     shared?: bool,
 *     synthetic?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     abstract?: bool,
 *     deprecated?: DeprecationType,
 *     factory?: CallbackType,
 *     configurator?: CallbackType,
 *     arguments?: ArgumentsType,
 *     properties?: array<string, mixed>,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     decorates?: string,
 *     decorates_tag?: string,
 *     decoration_inner_name?: string,
 *     decoration_priority?: int,
 *     decoration_on_invalid?: 'exception'|'ignore'|null,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 *     from_callable?: CallbackType,
 * }
 * @psalm-type AliasType = string|array{
 *     alias: string,
 *     public?: bool,
 *     deprecated?: DeprecationType,
 * }
 * @psalm-type PrototypeType = array{
 *     resource: string,
 *     namespace?: string,
 *     exclude?: string|list<string>,
 *     parent?: string,
 *     shared?: bool,
 *     lazy?: bool|string,
 *     public?: bool,
 *     abstract?: bool,
 *     deprecated?: DeprecationType,
 *     factory?: CallbackType,
 *     arguments?: ArgumentsType,
 *     properties?: array<string, mixed>,
 *     configurator?: CallbackType,
 *     calls?: list<CallType>,
 *     tags?: TagsType,
 *     resource_tags?: TagsType,
 *     autowire?: bool,
 *     autoconfigure?: bool,
 *     bind?: array<string, mixed>,
 *     constructor?: string,
 * }
 * @psalm-type StackType = array{
 *     stack: list<DefinitionType|AliasType|PrototypeType|array<class-string, ArgumentsType|null>>,
 *     public?: bool,
 *     deprecated?: DeprecationType,
 *     decorates?: string,
 *     decorates_tag?: string,
 *     decoration_inner_name?: string,
 *     decoration_priority?: int,
 *     decoration_on_invalid?: 'exception'|'ignore'|null,
 * }
 * @psalm-type ServicesConfig = array{
 *     _defaults?: DefaultsType,
 *     _instanceof?: array<class-string, InstanceofType>,
 *     ...<string, DefinitionType|AliasType|PrototypeType|StackType|ArgumentsType|null>
 * }
 * @psalm-type ExtensionType = array<string, mixed>
 * @psalm-type FrameworkConfig = array{
 *     secret?: scalar|Param|null,
 *     http_method_override?: bool|Param, // Set true to enable support for the '_method' request parameter to determine the intended HTTP method on POST requests. // Default: false
 *     allowed_http_method_override?: null|list<string|Param>,
 *     trust_x_sendfile_type_header?: scalar|Param|null, // Set true to enable support for xsendfile in binary file responses. // Default: "%env(bool:default::SYMFONY_TRUST_X_SENDFILE_TYPE_HEADER)%"
 *     ide?: scalar|Param|null, // Default: "%env(default::SYMFONY_IDE)%"
 *     test?: bool|Param,
 *     default_locale?: scalar|Param|null, // Default: "en"
 *     set_locale_from_accept_language?: bool|Param, // Whether to use the Accept-Language HTTP header to set the Request locale (only when the "_locale" request attribute is not passed). // Default: false
 *     set_content_language_from_locale?: bool|Param, // Whether to set the Content-Language HTTP header on the Response using the Request locale. // Default: false
 *     enabled_locales?: list<scalar|Param|null>,
 *     trusted_hosts?: Param|string|list<scalar|Param|null>,
 *     trusted_proxies?: mixed, // Default: ["%env(default::SYMFONY_TRUSTED_PROXIES)%"]
 *     trusted_headers?: Param|string|list<scalar|Param|null>,
 *     error_controller?: scalar|Param|null, // Default: "error_controller"
 *     handle_all_throwables?: bool|Param, // HttpKernel will handle all kinds of \Throwable. // Default: true
 *     csrf_protection?: bool|array{
 *         enabled?: scalar|Param|null, // Default: null
 *         stateless_token_ids?: list<scalar|Param|null>,
 *         check_header?: scalar|Param|null, // Whether to check the CSRF token in a header in addition to a cookie when using stateless protection. // Default: false
 *         cookie_name?: scalar|Param|null, // The name of the cookie to use when using stateless protection. // Default: "csrf-token"
 *     },
 *     form?: bool|array{ // Form configuration
 *         enabled?: bool|Param, // Default: true
 *         csrf_protection?: bool|array{
 *             enabled?: scalar|Param|null, // Default: null
 *             token_id?: scalar|Param|null, // Default: null
 *             field_name?: scalar|Param|null, // Default: "_token"
 *             field_attr?: array<string, scalar|Param|null>,
 *         },
 *     },
 *     http_cache?: bool|array{ // HTTP cache configuration
 *         enabled?: bool|Param, // Default: false
 *         debug?: bool|Param, // Default: "%kernel.debug%"
 *         trace_level?: "none"|"short"|"full"|Param,
 *         trace_header?: scalar|Param|null,
 *         default_ttl?: int|Param,
 *         private_headers?: list<scalar|Param|null>,
 *         skip_response_headers?: list<scalar|Param|null>,
 *         allow_reload?: bool|Param,
 *         allow_revalidate?: bool|Param,
 *         stale_while_revalidate?: int|Param,
 *         stale_if_error?: int|Param,
 *         terminate_on_cache_hit?: bool|Param, // Deprecated: Setting the "framework.http_cache.terminate_on_cache_hit.terminate_on_cache_hit" configuration option is deprecated. It will be removed in version 9.0.
 *     },
 *     esi?: bool|array{ // ESI configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     ssi?: bool|array{ // SSI configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     fragments?: bool|array{ // Fragments configuration
 *         enabled?: bool|Param, // Default: false
 *         hinclude_default_template?: scalar|Param|null, // Default: null
 *         path?: scalar|Param|null, // Default: "/_fragment"
 *     },
 *     profiler?: bool|array{ // Profiler configuration
 *         enabled?: bool|Param, // Default: false
 *         collect?: bool|Param, // Default: true
 *         collect_parameter?: scalar|Param|null, // The name of the parameter to use to enable or disable collection on a per request basis. // Default: null
 *         only_exceptions?: bool|Param, // Default: false
 *         only_main_requests?: bool|Param, // Default: false
 *         dsn?: scalar|Param|null, // Default: "file:%kernel.cache_dir%/profiler"
 *         collect_serializer_data?: true|Param, // Deprecated: Setting the "framework.profiler.collect_serializer_data.collect_serializer_data" configuration option is deprecated. It will be removed in version 9.0. // Default: true
 *     },
 *     workflows?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         workflows?: array<string, array{ // Default: []
 *             audit_trail?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *             },
 *             type?: "workflow"|"state_machine"|Param, // Default: "state_machine"
 *             marking_store?: array{
 *                 type?: "method"|Param,
 *                 property?: scalar|Param|null,
 *                 service?: scalar|Param|null,
 *             },
 *             supports?: Param|string|list<scalar|Param|null>,
 *             definition_validators?: list<scalar|Param|null>,
 *             support_strategy?: scalar|Param|null,
 *             initial_marking?: \BackedEnum|Param|string|list<scalar|Param|null>,
 *             events_to_dispatch?: null|list<string|Param>,
 *             places?: Param|string|list<array{ // Default: []
 *                 name?: scalar|Param|null,
 *                 metadata?: array<string, mixed>,
 *             }>,
 *             transitions?: list<array{ // Default: []
 *                 name?: string|Param,
 *                 guard?: string|Param, // An expression to block the transition.
 *                 from?: \BackedEnum|Param|string|list<array{ // Default: []
 *                     place?: string|Param,
 *                     weight?: int|Param, // Default: 1
 *                 }>,
 *                 to?: \BackedEnum|Param|string|list<array{ // Default: []
 *                     place?: string|Param,
 *                     weight?: int|Param, // Default: 1
 *                 }>,
 *                 weight?: int|Param, // Default: 1
 *                 metadata?: array<string, mixed>,
 *             }>,
 *             metadata?: array<string, mixed>,
 *         }>,
 *     },
 *     router?: bool|array{ // Router configuration
 *         enabled?: bool|Param, // Default: false
 *         resource?: scalar|Param|null,
 *         type?: scalar|Param|null,
 *         default_uri?: scalar|Param|null, // The default URI used to generate URLs in a non-HTTP context. // Default: null
 *         http_port?: scalar|Param|null, // Default: 80
 *         https_port?: scalar|Param|null, // Default: 443
 *         strict_requirements?: scalar|Param|null, // set to true to throw an exception when a parameter does not match the requirements set to false to disable exceptions when a parameter does not match the requirements (and return null instead) set to null to disable parameter checks against requirements 'true' is the preferred configuration in development mode, while 'false' or 'null' might be preferred in production // Default: true
 *         utf8?: bool|Param, // Default: true
 *     },
 *     session?: bool|array{ // Session configuration
 *         enabled?: bool|Param, // Default: false
 *         storage_factory_id?: scalar|Param|null, // Default: "session.storage.factory.native"
 *         handler_id?: scalar|Param|null, // Defaults to using the native session handler, or to the native *file* session handler if "save_path" is not null.
 *         name?: scalar|Param|null,
 *         cookie_lifetime?: scalar|Param|null,
 *         cookie_path?: scalar|Param|null,
 *         cookie_domain?: scalar|Param|null,
 *         cookie_secure?: true|false|"auto"|Param, // Default: "auto"
 *         cookie_httponly?: bool|Param, // Default: true
 *         cookie_samesite?: null|"lax"|"strict"|"none"|Param, // Default: "lax"
 *         use_cookies?: bool|Param,
 *         gc_divisor?: scalar|Param|null,
 *         gc_probability?: scalar|Param|null,
 *         gc_maxlifetime?: scalar|Param|null,
 *         save_path?: scalar|Param|null, // Defaults to "%kernel.cache_dir%/sessions" if the "handler_id" option is not null.
 *         metadata_update_threshold?: int|Param, // Seconds to wait between 2 session metadata updates. // Default: 0
 *     },
 *     request?: bool|array{ // Request configuration
 *         enabled?: bool|Param, // Default: false
 *         formats?: array<string, Param|string|list<scalar|Param|null>>,
 *     },
 *     assets?: bool|array{ // Assets configuration
 *         enabled?: bool|Param, // Default: true
 *         strict_mode?: bool|Param, // Throw an exception if an entry is missing from the manifest.json. // Default: false
 *         version_strategy?: scalar|Param|null, // Default: null
 *         version?: scalar|Param|null, // Default: null
 *         version_format?: scalar|Param|null, // Default: "%%s?%%s"
 *         json_manifest_path?: scalar|Param|null, // Default: null
 *         base_path?: scalar|Param|null, // Default: ""
 *         base_urls?: Param|string|list<scalar|Param|null>,
 *         packages?: array<string, array{ // Default: []
 *             strict_mode?: bool|Param, // Throw an exception if an entry is missing from the manifest.json. // Default: false
 *             version_strategy?: scalar|Param|null, // Default: null
 *             version?: scalar|Param|null,
 *             version_format?: scalar|Param|null, // Default: null
 *             json_manifest_path?: scalar|Param|null, // Default: null
 *             base_path?: scalar|Param|null, // Default: ""
 *             base_urls?: Param|string|list<scalar|Param|null>,
 *         }>,
 *     },
 *     asset_mapper?: bool|array{ // Asset Mapper configuration
 *         enabled?: bool|Param, // Default: true
 *         paths?: Param|string|array<string, scalar|Param|null>,
 *         excluded_patterns?: list<scalar|Param|null>,
 *         exclude_dotfiles?: bool|Param, // If true, any files starting with "." will be excluded from the asset mapper. // Default: true
 *         server?: bool|Param, // If true, a "dev server" will return the assets from the public directory (true in "debug" mode only by default). // Default: true
 *         public_prefix?: scalar|Param|null, // The public path where the assets will be written to (and served from when "server" is true). // Default: "/assets/"
 *         missing_import_mode?: "strict"|"warn"|"ignore"|Param, // Behavior if an asset cannot be found when imported from JavaScript or CSS files - e.g. "import './non-existent.js'". "strict" means an exception is thrown, "warn" means a warning is logged, "ignore" means the import is left as-is. // Default: "warn"
 *         extensions?: array<string, scalar|Param|null>,
 *         importmap_path?: scalar|Param|null, // The path of the importmap.php file. // Default: "%kernel.project_dir%/importmap.php"
 *         importmap_polyfill?: scalar|Param|null, // The importmap name that will be used to load the polyfill. Set to false to disable. // Default: "es-module-shims"
 *         importmap_script_attributes?: array<string, scalar|Param|null>,
 *         vendor_dir?: scalar|Param|null, // The directory to store JavaScript vendors. // Default: "%kernel.project_dir%/assets/vendor"
 *         precompress?: bool|array{ // Precompress assets with Brotli, Zstandard and gzip.
 *             enabled?: bool|Param, // Default: false
 *             formats?: list<scalar|Param|null>,
 *             extensions?: list<scalar|Param|null>,
 *         },
 *     },
 *     translator?: bool|array{ // Translator configuration
 *         enabled?: bool|Param, // Default: true
 *         fallbacks?: Param|string|list<scalar|Param|null>,
 *         logging?: bool|Param, // Default: false
 *         formatter?: scalar|Param|null, // Default: "translator.formatter.default"
 *         cache_dir?: scalar|Param|null, // Default: "%kernel.cache_dir%/translations"
 *         default_path?: scalar|Param|null, // The default path used to load translations. // Default: "%kernel.project_dir%/translations"
 *         paths?: list<scalar|Param|null>,
 *         pseudo_localization?: bool|array{
 *             enabled?: bool|Param, // Default: false
 *             accents?: bool|Param, // Default: true
 *             expansion_factor?: float|Param, // Default: 1.0
 *             brackets?: bool|Param, // Default: true
 *             parse_html?: bool|Param, // Default: false
 *             localizable_html_attributes?: list<scalar|Param|null>,
 *         },
 *         providers?: array<string, array{ // Default: []
 *             dsn?: scalar|Param|null,
 *             domains?: list<scalar|Param|null>,
 *             locales?: list<scalar|Param|null>,
 *         }>,
 *         globals?: array<string, Param|string|array{ // Default: []
 *             value?: mixed,
 *             message?: string|Param,
 *             parameters?: array<string, scalar|Param|null>,
 *             domain?: string|Param,
 *         }>,
 *     },
 *     validation?: bool|array{ // Validation configuration
 *         enabled?: bool|Param, // Default: true
 *         enable_attributes?: bool|Param, // Default: true
 *         static_method?: Param|string|list<scalar|Param|null>,
 *         translation_domain?: scalar|Param|null, // Default: "validators"
 *         email_validation_mode?: "html5"|"html5-allow-no-tld"|"strict"|Param, // Default: "html5"
 *         mapping?: array{
 *             paths?: list<scalar|Param|null>,
 *         },
 *         not_compromised_password?: bool|array{
 *             enabled?: bool|Param, // When disabled, compromised passwords will be accepted as valid. // Default: true
 *             endpoint?: scalar|Param|null, // API endpoint for the NotCompromisedPassword Validator. // Default: null
 *         },
 *         disable_translation?: bool|Param, // Default: false
 *         property_metadata_existence_check?: bool|Param, // When enabled, validateProperty() and validatePropertyValue() throw an exception if no metadata is found for the given property. // Default: false
 *         auto_mapping?: array<string, array{ // Default: []
 *             services?: list<scalar|Param|null>,
 *         }>,
 *     },
 *     serializer?: bool|array{ // Serializer configuration
 *         enabled?: bool|Param, // Default: true
 *         enable_attributes?: bool|Param, // Default: true
 *         name_converter?: scalar|Param|null,
 *         circular_reference_handler?: scalar|Param|null,
 *         max_depth_handler?: scalar|Param|null,
 *         mapping?: array{
 *             paths?: list<scalar|Param|null>,
 *         },
 *         default_context?: array<string, mixed>,
 *         named_serializers?: array<string, array{ // Default: []
 *             name_converter?: scalar|Param|null,
 *             default_context?: array<string, mixed>,
 *             include_built_in_normalizers?: bool|Param, // Whether to include the built-in normalizers // Default: true
 *             include_built_in_encoders?: bool|Param, // Whether to include the built-in encoders // Default: true
 *         }>,
 *     },
 *     property_access?: bool|array{ // Property access configuration
 *         enabled?: bool|Param, // Default: true
 *         magic_call?: bool|Param, // Default: false
 *         magic_get?: bool|Param, // Default: true
 *         magic_set?: bool|Param, // Default: true
 *         throw_exception_on_invalid_index?: bool|Param, // Default: false
 *         throw_exception_on_invalid_property_path?: bool|Param, // Default: true
 *     },
 *     type_info?: bool|array{ // Type info configuration
 *         enabled?: bool|Param, // Default: true
 *         aliases?: array<string, scalar|Param|null>,
 *     },
 *     property_info?: bool|array{ // Property info configuration
 *         enabled?: bool|Param, // Default: true
 *         with_constructor_extractor?: bool|Param, // Registers the constructor extractor. // Default: true
 *     },
 *     cache?: array{ // Cache configuration
 *         prefix_seed?: scalar|Param|null, // Used to namespace cache keys when using several apps with the same shared backend. // Default: "_%kernel.project_dir%.%kernel.container_class%"
 *         app?: scalar|Param|null, // App related cache pools configuration. // Default: "cache.adapter.filesystem"
 *         system?: scalar|Param|null, // System related cache pools configuration. // Default: "cache.adapter.system"
 *         directory?: scalar|Param|null, // Default: "%kernel.share_dir%/pools/app"
 *         default_psr6_provider?: scalar|Param|null,
 *         default_redis_provider?: scalar|Param|null, // Default: "redis://localhost"
 *         default_valkey_provider?: scalar|Param|null, // Default: "valkey://localhost"
 *         default_memcached_provider?: scalar|Param|null, // Default: "memcached://localhost"
 *         default_doctrine_dbal_provider?: scalar|Param|null, // Default: "database_connection"
 *         default_pdo_provider?: scalar|Param|null, // Default: null
 *         pools?: array<string, array{ // Default: []
 *             adapters?: Param|string|list<scalar|Param|null>,
 *             tags?: scalar|Param|null, // Default: null
 *             public?: bool|Param, // Default: false
 *             default_lifetime?: scalar|Param|null, // Default lifetime of the pool.
 *             provider?: scalar|Param|null, // Overwrite the setting from the default provider for this adapter.
 *             early_expiration_message_bus?: scalar|Param|null,
 *             clearer?: scalar|Param|null,
 *             marshaller?: scalar|Param|null, // The marshaller service to use for this pool.
 *         }>,
 *     },
 *     php_errors?: array{ // PHP errors handling configuration
 *         log?: mixed, // Use the application logger instead of the PHP logger for logging PHP errors. // Default: true
 *         throw?: bool|Param, // Throw PHP errors as \ErrorException instances. // Default: true
 *     },
 *     exceptions?: array<string, array{ // Default: []
 *         log_level?: scalar|Param|null, // The level of log message. Null to let Symfony decide. // Default: null
 *         status_code?: scalar|Param|null, // The status code of the response. Null or 0 to let Symfony decide. // Default: null
 *         log_channel?: scalar|Param|null, // The channel of log message. Null to let Symfony decide. // Default: null
 *     }>,
 *     web_link?: bool|array{ // Web links configuration
 *         enabled?: bool|Param, // Default: true
 *     },
 *     lock?: Param|bool|string|array{ // Lock configuration
 *         enabled?: bool|Param, // Default: true
 *         resources?: Param|string|array<string, Param|string|list<scalar|Param|null>>,
 *     },
 *     semaphore?: Param|bool|string|array{ // Semaphore configuration
 *         enabled?: bool|Param, // Default: false
 *         resources?: Param|string|array<string, scalar|Param|null>,
 *     },
 *     messenger?: bool|array{ // Messenger configuration
 *         enabled?: bool|Param, // Default: true
 *         routing?: array<string, Param|string|list<scalar|Param|null>>,
 *         serializer?: array{
 *             default_serializer?: scalar|Param|null, // Service id to use as the default serializer for the transports. // Default: "messenger.transport.native_php_serializer"
 *             symfony_serializer?: array{
 *                 format?: scalar|Param|null, // Serialization format for the messenger.transport.symfony_serializer service (which is not the serializer used by default). // Default: "json"
 *                 context?: array<string, mixed>,
 *             },
 *         },
 *         transports?: array<string, Param|string|array{ // Default: []
 *             dsn?: scalar|Param|null,
 *             serializer?: scalar|Param|null, // Service id of a custom serializer to use. // Default: null
 *             options?: array<string, mixed>,
 *             failure_transport?: scalar|Param|null, // Transport name to send failed messages to (after all retries have failed). // Default: null
 *             retry_strategy?: Param|string|array{
 *                 service?: scalar|Param|null, // Service id to override the retry strategy entirely. // Default: null
 *                 max_retries?: int|Param, // Default: 3
 *                 delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                 multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: this delay = (delay * (multiple ^ retries)). // Default: 2
 *                 max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                 jitter?: float|Param, // Randomness to apply to the delay (between 0 and 1). // Default: 0.1
 *             },
 *             rate_limiter?: scalar|Param|null, // Rate limiter name to use when processing messages. // Default: null
 *         }>,
 *         failure_transport?: scalar|Param|null, // Transport name to send failed messages to (after all retries have failed). // Default: null
 *         stop_worker_on_signals?: Param|int|string|list<scalar|Param|null>,
 *         default_bus?: scalar|Param|null, // Default: null
 *         buses?: array<string, array{ // Default: {"messenger.bus.default":{"default_middleware":{"enabled":true,"allow_no_handlers":false,"allow_no_senders":true},"middleware":[]}}
 *             default_middleware?: Param|bool|string|array{
 *                 enabled?: bool|Param, // Default: true
 *                 allow_no_handlers?: bool|Param, // Default: false
 *                 allow_no_senders?: bool|Param, // Default: true
 *             },
 *             middleware?: Param|string|list<Param|string|array{ // Default: []
 *                 id?: scalar|Param|null,
 *                 arguments?: list<mixed>,
 *             }>,
 *         }>,
 *     },
 *     scheduler?: bool|array{ // Scheduler configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     disallow_search_engine_index?: bool|Param, // Enabled by default when debug is enabled. // Default: true
 *     http_client?: bool|array{ // HTTP Client configuration
 *         enabled?: bool|Param, // Default: true
 *         max_host_connections?: int|Param, // The maximum number of connections to a single host.
 *         default_options?: array{
 *             headers?: array<string, mixed>,
 *             vars?: array<string, mixed>,
 *             max_redirects?: int|Param, // The maximum number of redirects to follow.
 *             http_version?: scalar|Param|null, // The default HTTP version, typically 1.1 or 2.0, leave to null for the best version.
 *             resolve?: array<string, scalar|Param|null>,
 *             proxy?: scalar|Param|null, // The URL of the proxy to pass requests through or null for automatic detection.
 *             no_proxy?: scalar|Param|null, // A comma separated list of hosts that do not require a proxy to be reached.
 *             timeout?: float|Param, // The idle timeout, defaults to the "default_socket_timeout" ini parameter.
 *             max_duration?: float|Param, // The maximum execution time for the request+response as a whole.
 *             bindto?: scalar|Param|null, // A network interface name, IP address, a host name or a UNIX socket to bind to.
 *             verify_peer?: bool|Param, // Indicates if the peer should be verified in a TLS context.
 *             verify_host?: bool|Param, // Indicates if the host should exist as a certificate common name.
 *             cafile?: scalar|Param|null, // A certificate authority file.
 *             capath?: scalar|Param|null, // A directory that contains multiple certificate authority files.
 *             local_cert?: scalar|Param|null, // A PEM formatted certificate file.
 *             local_pk?: scalar|Param|null, // A private key file.
 *             passphrase?: scalar|Param|null, // The passphrase used to encrypt the "local_pk" file.
 *             ciphers?: scalar|Param|null, // A list of TLS ciphers separated by colons, commas or spaces (e.g. "RC3-SHA:TLS13-AES-128-GCM-SHA256"...)
 *             peer_fingerprint?: array{ // Associative array: hashing algorithm => hash(es).
 *                 sha1?: mixed,
 *                 pin-sha256?: mixed,
 *                 md5?: mixed,
 *             },
 *             crypto_method?: scalar|Param|null, // The minimum version of TLS to accept; must be one of STREAM_CRYPTO_METHOD_TLSv*_CLIENT constants.
 *             extra?: array<string, mixed>,
 *             rate_limiter?: scalar|Param|null, // Rate limiter name to use for throttling requests. // Default: null
 *             caching?: bool|array{ // Caching configuration.
 *                 enabled?: bool|Param, // Default: false
 *                 cache_pool?: string|Param, // The taggable cache pool to use for storing the responses. // Default: "cache.http_client"
 *                 shared?: bool|Param, // Indicates whether the cache is shared (public) or private. // Default: true
 *                 max_ttl?: int|Param, // The maximum TTL (in seconds) allowed for cached responses. // Default: 86400
 *             },
 *             retry_failed?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 retry_strategy?: scalar|Param|null, // service id to override the retry strategy. // Default: null
 *                 http_codes?: Param|int|string|array<string, array{ // Default: []
 *                     code?: int|Param,
 *                     methods?: Param|string|list<string|Param>,
 *                 }>,
 *                 max_retries?: int|Param, // Default: 3
 *                 delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                 multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: delay * (multiple ^ retries). // Default: 2
 *                 max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                 jitter?: float|Param, // Randomness in percent (between 0 and 1) to apply to the delay. // Default: 0.1
 *             },
 *         },
 *         mock_response_factory?: scalar|Param|null, // `true` to always return empty 200 responses, or the id of the service to use to generate mock responses - which should be either an invokable or an iterable.
 *         scoped_clients?: array<string, Param|string|array{ // Default: []
 *             scope?: scalar|Param|null, // The regular expression that the request URL must match before adding the other options. When none is provided, the base URI is used instead.
 *             base_uri?: scalar|Param|null, // The URI to resolve relative URLs, following rules in RFC 3985, section 2.
 *             auth_basic?: scalar|Param|null, // An HTTP Basic authentication "username:password".
 *             auth_bearer?: scalar|Param|null, // A token enabling HTTP Bearer authorization.
 *             auth_ntlm?: scalar|Param|null, // A "username:password" pair to use Microsoft NTLM authentication (requires the cURL extension).
 *             query?: array<string, scalar|Param|null>,
 *             headers?: array<string, mixed>,
 *             max_redirects?: int|Param, // The maximum number of redirects to follow.
 *             http_version?: scalar|Param|null, // The default HTTP version, typically 1.1 or 2.0, leave to null for the best version.
 *             resolve?: array<string, scalar|Param|null>,
 *             proxy?: scalar|Param|null, // The URL of the proxy to pass requests through or null for automatic detection.
 *             no_proxy?: scalar|Param|null, // A comma separated list of hosts that do not require a proxy to be reached.
 *             timeout?: float|Param, // The idle timeout, defaults to the "default_socket_timeout" ini parameter.
 *             max_duration?: float|Param, // The maximum execution time for the request+response as a whole.
 *             bindto?: scalar|Param|null, // A network interface name, IP address, a host name or a UNIX socket to bind to.
 *             verify_peer?: bool|Param, // Indicates if the peer should be verified in a TLS context.
 *             verify_host?: bool|Param, // Indicates if the host should exist as a certificate common name.
 *             cafile?: scalar|Param|null, // A certificate authority file.
 *             capath?: scalar|Param|null, // A directory that contains multiple certificate authority files.
 *             local_cert?: scalar|Param|null, // A PEM formatted certificate file.
 *             local_pk?: scalar|Param|null, // A private key file.
 *             passphrase?: scalar|Param|null, // The passphrase used to encrypt the "local_pk" file.
 *             ciphers?: scalar|Param|null, // A list of TLS ciphers separated by colons, commas or spaces (e.g. "RC3-SHA:TLS13-AES-128-GCM-SHA256"...).
 *             peer_fingerprint?: array{ // Associative array: hashing algorithm => hash(es).
 *                 sha1?: mixed,
 *                 pin-sha256?: mixed,
 *                 md5?: mixed,
 *             },
 *             crypto_method?: scalar|Param|null, // The minimum version of TLS to accept; must be one of STREAM_CRYPTO_METHOD_TLSv*_CLIENT constants.
 *             mock_response_factory?: scalar|Param|null, // `true` to always return empty 200 responses, `false` to disable mocking, or the id of the service to use to generate mock responses (invokable or iterable).
 *             extra?: array<string, mixed>,
 *             rate_limiter?: scalar|Param|null, // Rate limiter name to use for throttling requests. // Default: null
 *             caching?: bool|array{ // Caching configuration.
 *                 enabled?: bool|Param, // Default: false
 *                 cache_pool?: string|Param, // The taggable cache pool to use for storing the responses. // Default: "cache.http_client"
 *                 shared?: bool|Param, // Indicates whether the cache is shared (public) or private. // Default: true
 *                 max_ttl?: int|Param, // The maximum TTL (in seconds) allowed for cached responses. // Default: 86400
 *             },
 *             retry_failed?: bool|array{
 *                 enabled?: bool|Param, // Default: false
 *                 retry_strategy?: scalar|Param|null, // service id to override the retry strategy. // Default: null
 *                 http_codes?: Param|int|string|array<string, array{ // Default: []
 *                     code?: int|Param,
 *                     methods?: Param|string|list<string|Param>,
 *                 }>,
 *                 max_retries?: int|Param, // Default: 3
 *                 delay?: int|Param, // Time in ms to delay (or the initial value when multiplier is used). // Default: 1000
 *                 multiplier?: float|Param, // If greater than 1, delay will grow exponentially for each retry: delay * (multiple ^ retries). // Default: 2
 *                 max_delay?: int|Param, // Max time in ms that a retry should ever be delayed (0 = infinite). // Default: 0
 *                 jitter?: float|Param, // Randomness in percent (between 0 and 1) to apply to the delay. // Default: 0.1
 *             },
 *         }>,
 *     },
 *     mailer?: bool|array{ // Mailer configuration
 *         enabled?: bool|Param, // Default: true
 *         message_bus?: scalar|Param|null, // The message bus to use. Defaults to the default bus if the Messenger component is installed. // Default: null
 *         dsn?: scalar|Param|null, // Default: null
 *         transports?: array<string, scalar|Param|null>,
 *         envelope?: array{ // Mailer Envelope configuration
 *             sender?: scalar|Param|null,
 *             recipients?: Param|string|list<scalar|Param|null>,
 *             allowed_recipients?: Param|string|list<scalar|Param|null>,
 *         },
 *         headers?: array<string, Param|string|array{ // Default: []
 *             value?: mixed,
 *         }>,
 *         dkim_signer?: bool|array{ // DKIM signer configuration
 *             enabled?: bool|Param, // Default: false
 *             key?: scalar|Param|null, // Key content, or path to key (in PEM format with the `file://` prefix) // Default: ""
 *             domain?: scalar|Param|null, // Default: ""
 *             select?: scalar|Param|null, // Default: ""
 *             passphrase?: scalar|Param|null, // The private key passphrase // Default: ""
 *             options?: array<string, mixed>,
 *         },
 *         smime_signer?: bool|array{ // S/MIME signer configuration
 *             enabled?: bool|Param, // Default: false
 *             key?: scalar|Param|null, // Path to key (in PEM format) // Default: ""
 *             certificate?: scalar|Param|null, // Path to certificate (in PEM format without the `file://` prefix) // Default: ""
 *             passphrase?: scalar|Param|null, // The private key passphrase // Default: null
 *             extra_certificates?: scalar|Param|null, // Default: null
 *             sign_options?: int|Param, // Default: null
 *         },
 *         smime_encrypter?: bool|array{ // S/MIME encrypter configuration
 *             enabled?: bool|Param, // Default: false
 *             repository?: scalar|Param|null, // S/MIME certificate repository service. This service shall implement the `Symfony\Component\Mailer\EventListener\SmimeCertificateRepositoryInterface`. // Default: ""
 *             cipher?: int|Param, // A set of algorithms used to encrypt the message // Default: null
 *         },
 *     },
 *     secrets?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         vault_directory?: scalar|Param|null, // Default: "%kernel.project_dir%/config/secrets/%kernel.runtime_environment%"
 *         local_dotenv_file?: scalar|Param|null, // Default: "%kernel.project_dir%/.env.%kernel.environment%.local"
 *         decryption_env_var?: scalar|Param|null, // Default: "base64:default::SYMFONY_DECRYPTION_SECRET"
 *     },
 *     notifier?: bool|array{ // Notifier configuration
 *         enabled?: bool|Param, // Default: true
 *         message_bus?: scalar|Param|null, // The message bus to use. Defaults to the default bus if the Messenger component is installed. // Default: null
 *         chatter_transports?: array<string, scalar|Param|null>,
 *         texter_transports?: array<string, scalar|Param|null>,
 *         notification_on_failed_messages?: bool|Param, // Default: false
 *         channel_policy?: array<string, Param|string|list<scalar|Param|null>>,
 *         admin_recipients?: list<array{ // Default: []
 *             email?: scalar|Param|null,
 *             phone?: scalar|Param|null, // Default: ""
 *         }>,
 *     },
 *     rate_limiter?: bool|array{ // Rate limiter configuration
 *         enabled?: bool|Param, // Default: false
 *         limiters?: array<string, array{ // Default: []
 *             lock_factory?: scalar|Param|null, // The service ID of the lock factory used by this limiter (or null to disable locking). // Default: "auto"
 *             cache_pool?: scalar|Param|null, // The cache pool to use for storing the current limiter state. // Default: "cache.rate_limiter"
 *             storage_service?: scalar|Param|null, // The service ID of a custom storage implementation, this precedes any configured "cache_pool". // Default: null
 *             policy?: "fixed_window"|"token_bucket"|"sliding_window"|"compound"|"no_limit"|Param, // The algorithm to be used by this limiter.
 *             limiters?: Param|string|list<scalar|Param|null>,
 *             limit?: int|Param, // The maximum allowed hits in a fixed interval or burst.
 *             interval?: scalar|Param|null, // Configures the fixed interval if "policy" is set to "fixed_window" or "sliding_window". The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).
 *             rate?: array{ // Configures the fill rate if "policy" is set to "token_bucket".
 *                 interval?: scalar|Param|null, // Configures the rate interval. The value must be a number followed by "second", "minute", "hour", "day", "week" or "month" (or their plural equivalent).
 *                 amount?: int|Param, // Amount of tokens to add each interval. // Default: 1
 *             },
 *             anchor_at?: scalar|Param|null, // Aligns the "fixed_window" policy to a calendar (e.g. "2024-01-05 00:00:00 UTC" combined with `interval: 1 month` resets the counter on the 5th of each month). UTC if not specified. // Default: null
 *         }>,
 *     },
 *     uid?: bool|array{ // Uid configuration
 *         enabled?: bool|Param, // Default: true
 *         default_uuid_version?: 7|6|4|1|Param, // Default: 7
 *         name_based_uuid_version?: 5|3|Param, // Default: 5
 *         name_based_uuid_namespace?: scalar|Param|null,
 *         time_based_uuid_version?: 7|6|1|Param, // Default: 7
 *         time_based_uuid_node?: scalar|Param|null,
 *         uuid47_secret?: scalar|Param|null, // A high-entropy secret used by the "uuid47_transformer" service. Defaults to "kernel.secret". // Default: null
 *     },
 *     html_sanitizer?: bool|array{ // HtmlSanitizer configuration
 *         enabled?: bool|Param, // Default: false
 *         sanitizers?: array<string, array{ // Default: []
 *             default_action?: "drop"|"block"|"allow"|Param, // Defines how the sanitizer must behave by default.
 *             allow_safe_elements?: bool|Param, // Allows "safe" elements and attributes. // Default: false
 *             allow_static_elements?: bool|Param, // Allows all static elements and attributes from the W3C Sanitizer API standard. // Default: false
 *             allow_elements?: array<string, mixed>,
 *             block_elements?: Param|string|list<string|Param>,
 *             drop_elements?: Param|string|list<string|Param>,
 *             allow_attributes?: array<string, mixed>,
 *             drop_attributes?: array<string, mixed>,
 *             force_attributes?: array<string, array<string, string|Param>>,
 *             force_https_urls?: bool|Param, // Transforms URLs using the HTTP scheme to use the HTTPS scheme instead. // Default: false
 *             allowed_link_schemes?: Param|string|list<string|Param>,
 *             allowed_link_hosts?: Param|null|string|list<string|Param>,
 *             allow_relative_links?: bool|Param, // Allows relative URLs to be used in links href attributes. // Default: false
 *             allowed_media_schemes?: Param|string|list<string|Param>,
 *             allowed_media_hosts?: Param|null|string|list<string|Param>,
 *             allow_relative_medias?: bool|Param, // Allows relative URLs to be used in media source attributes (img, audio, video, ...). // Default: false
 *             with_attribute_sanitizers?: Param|string|list<string|Param>,
 *             without_attribute_sanitizers?: Param|string|list<string|Param>,
 *             max_input_length?: int|Param, // The maximum length allowed for the sanitized input. // Default: 0
 *         }>,
 *     },
 *     webhook?: bool|array{ // Webhook configuration
 *         enabled?: bool|Param, // Default: false
 *         message_bus?: scalar|Param|null, // The message bus to use. // Default: "messenger.default_bus"
 *         event_header_name?: scalar|Param|null, // Default: "Webhook-Event"
 *         id_header_name?: scalar|Param|null, // Default: "Webhook-Id"
 *         signature_header_name?: scalar|Param|null, // Default: "Webhook-Signature"
 *         signing_algorithm?: scalar|Param|null, // Default: "sha256"
 *         routing?: array<string, array{ // Default: []
 *             service?: scalar|Param|null,
 *             secret?: scalar|Param|null, // Default: ""
 *         }>,
 *     },
 *     remote-event?: bool|array{ // RemoteEvent configuration
 *         enabled?: bool|Param, // Default: false
 *     },
 *     json_streamer?: bool|array{ // JSON streamer configuration
 *         enabled?: bool|Param, // Default: false
 *         default_options?: array{
 *             include_null_properties?: bool|Param, // Encode the properties with null value // Default: false
 *             ...<string, mixed>
 *         },
 *     },
 * }
 * @psalm-type DoctrineConfig = array{
 *     dbal?: array{
 *         default_connection?: scalar|Param|null,
 *         types?: array<string, Param|string|array{ // Default: []
 *             class?: scalar|Param|null,
 *         }>,
 *         driver_schemes?: array<string, scalar|Param|null>,
 *         connections?: array<string, array{ // Default: []
 *             url?: scalar|Param|null, // A URL with connection information; any parameter value parsed from this string will override explicitly set parameters
 *             dbname?: scalar|Param|null,
 *             host?: scalar|Param|null, // Defaults to "localhost" at runtime.
 *             port?: scalar|Param|null, // Defaults to null at runtime.
 *             user?: scalar|Param|null, // Defaults to "root" at runtime.
 *             password?: scalar|Param|null, // Defaults to null at runtime.
 *             dbname_suffix?: scalar|Param|null, // Adds the given suffix to the configured database name, this option has no effects for the SQLite platform
 *             application_name?: scalar|Param|null,
 *             charset?: scalar|Param|null,
 *             path?: scalar|Param|null,
 *             memory?: bool|Param,
 *             unix_socket?: scalar|Param|null, // The unix socket to use for MySQL
 *             persistent?: bool|Param, // True to use as persistent connection for the ibm_db2 driver
 *             protocol?: scalar|Param|null, // The protocol to use for the ibm_db2 driver (default to TCPIP if omitted)
 *             service?: bool|Param, // True to use SERVICE_NAME as connection parameter instead of SID for Oracle
 *             servicename?: scalar|Param|null, // Overrules dbname parameter if given and used as SERVICE_NAME or SID connection parameter for Oracle depending on the service parameter.
 *             sessionMode?: scalar|Param|null, // The session mode to use for the oci8 driver
 *             server?: scalar|Param|null, // The name of a running database server to connect to for SQL Anywhere.
 *             default_dbname?: scalar|Param|null, // Override the default database (postgres) to connect to for PostgreSQL connection.
 *             sslmode?: scalar|Param|null, // Determines whether or with what priority a SSL TCP/IP connection will be negotiated with the server for PostgreSQL.
 *             sslrootcert?: scalar|Param|null, // The name of a file containing SSL certificate authority (CA) certificate(s). If the file exists, the server's certificate will be verified to be signed by one of these authorities.
 *             sslcert?: scalar|Param|null, // The path to the SSL client certificate file for PostgreSQL.
 *             sslkey?: scalar|Param|null, // The path to the SSL client key file for PostgreSQL.
 *             sslcrl?: scalar|Param|null, // The file name of the SSL certificate revocation list for PostgreSQL.
 *             pooled?: bool|Param, // True to use a pooled server with the oci8/pdo_oracle driver
 *             MultipleActiveResultSets?: bool|Param, // Configuring MultipleActiveResultSets for the pdo_sqlsrv driver
 *             instancename?: scalar|Param|null, // Optional parameter, complete whether to add the INSTANCE_NAME parameter in the connection. It is generally used to connect to an Oracle RAC server to select the name of a particular instance.
 *             connectstring?: scalar|Param|null, // Complete Easy Connect connection descriptor, see https://docs.oracle.com/database/121/NETAG/naming.htm.When using this option, you will still need to provide the user and password parameters, but the other parameters will no longer be used. Note that when using this parameter, the getHost and getPort methods from Doctrine\DBAL\Connection will no longer function as expected.
 *             driver?: scalar|Param|null, // Default: "pdo_mysql"
 *             auto_commit?: bool|Param,
 *             schema_filter?: scalar|Param|null,
 *             logging?: bool|Param, // Default: true
 *             profiling?: bool|Param, // Default: true
 *             profiling_collect_backtrace?: bool|Param, // Enables collecting backtraces when profiling is enabled // Default: false
 *             profiling_collect_schema_errors?: bool|Param, // Enables collecting schema errors when profiling is enabled // Default: true
 *             server_version?: scalar|Param|null,
 *             idle_connection_ttl?: int|Param, // Default: 600
 *             driver_class?: scalar|Param|null,
 *             wrapper_class?: scalar|Param|null,
 *             keep_replica?: bool|Param,
 *             options?: array<string, mixed>,
 *             mapping_types?: array<string, scalar|Param|null>,
 *             default_table_options?: array<string, scalar|Param|null>,
 *             schema_manager_factory?: scalar|Param|null, // Default: "doctrine.dbal.default_schema_manager_factory"
 *             result_cache?: scalar|Param|null,
 *             replicas?: array<string, array{ // Default: []
 *                 url?: scalar|Param|null, // A URL with connection information; any parameter value parsed from this string will override explicitly set parameters
 *                 dbname?: scalar|Param|null,
 *                 host?: scalar|Param|null, // Defaults to "localhost" at runtime.
 *                 port?: scalar|Param|null, // Defaults to null at runtime.
 *                 user?: scalar|Param|null, // Defaults to "root" at runtime.
 *                 password?: scalar|Param|null, // Defaults to null at runtime.
 *                 dbname_suffix?: scalar|Param|null, // Adds the given suffix to the configured database name, this option has no effects for the SQLite platform
 *                 application_name?: scalar|Param|null,
 *                 charset?: scalar|Param|null,
 *                 path?: scalar|Param|null,
 *                 memory?: bool|Param,
 *                 unix_socket?: scalar|Param|null, // The unix socket to use for MySQL
 *                 persistent?: bool|Param, // True to use as persistent connection for the ibm_db2 driver
 *                 protocol?: scalar|Param|null, // The protocol to use for the ibm_db2 driver (default to TCPIP if omitted)
 *                 service?: bool|Param, // True to use SERVICE_NAME as connection parameter instead of SID for Oracle
 *                 servicename?: scalar|Param|null, // Overrules dbname parameter if given and used as SERVICE_NAME or SID connection parameter for Oracle depending on the service parameter.
 *                 sessionMode?: scalar|Param|null, // The session mode to use for the oci8 driver
 *                 server?: scalar|Param|null, // The name of a running database server to connect to for SQL Anywhere.
 *                 default_dbname?: scalar|Param|null, // Override the default database (postgres) to connect to for PostgreSQL connection.
 *                 sslmode?: scalar|Param|null, // Determines whether or with what priority a SSL TCP/IP connection will be negotiated with the server for PostgreSQL.
 *                 sslrootcert?: scalar|Param|null, // The name of a file containing SSL certificate authority (CA) certificate(s). If the file exists, the server's certificate will be verified to be signed by one of these authorities.
 *                 sslcert?: scalar|Param|null, // The path to the SSL client certificate file for PostgreSQL.
 *                 sslkey?: scalar|Param|null, // The path to the SSL client key file for PostgreSQL.
 *                 sslcrl?: scalar|Param|null, // The file name of the SSL certificate revocation list for PostgreSQL.
 *                 pooled?: bool|Param, // True to use a pooled server with the oci8/pdo_oracle driver
 *                 MultipleActiveResultSets?: bool|Param, // Configuring MultipleActiveResultSets for the pdo_sqlsrv driver
 *                 instancename?: scalar|Param|null, // Optional parameter, complete whether to add the INSTANCE_NAME parameter in the connection. It is generally used to connect to an Oracle RAC server to select the name of a particular instance.
 *                 connectstring?: scalar|Param|null, // Complete Easy Connect connection descriptor, see https://docs.oracle.com/database/121/NETAG/naming.htm.When using this option, you will still need to provide the user and password parameters, but the other parameters will no longer be used. Note that when using this parameter, the getHost and getPort methods from Doctrine\DBAL\Connection will no longer function as expected.
 *                 ...<string, mixed>
 *             }>,
 *             ...<string, mixed>
 *         }>,
 *         ...<string, mixed>
 *     },
 *     orm?: array{
 *         default_entity_manager?: scalar|Param|null,
 *         enable_native_lazy_objects?: bool|Param, // Deprecated: The "enable_native_lazy_objects" option is deprecated and will be removed in DoctrineBundle 4.0, as native lazy objects are now always enabled. // Default: true
 *         controller_resolver?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *             auto_mapping?: bool|Param, // Deprecated: The "doctrine.orm.controller_resolver.auto_mapping.auto_mapping" option is deprecated and will be removed in DoctrineBundle 4.0, as it only accepts `false` since 3.0. // Set to true to enable using route placeholders as lookup criteria when the primary key doesn't match the argument name // Default: false
 *             evict_cache?: bool|Param, // Set to true to fetch the entity from the database instead of using the cache, if any // Default: false
 *         },
 *         entity_managers?: array<string, array{ // Default: []
 *             query_cache_driver?: Param|string|array{
 *                 type?: scalar|Param|null, // Default: null
 *                 id?: scalar|Param|null,
 *                 pool?: scalar|Param|null,
 *             },
 *             metadata_cache_driver?: Param|string|array{
 *                 type?: scalar|Param|null, // Default: null
 *                 id?: scalar|Param|null,
 *                 pool?: scalar|Param|null,
 *             },
 *             result_cache_driver?: Param|string|array{
 *                 type?: scalar|Param|null, // Default: null
 *                 id?: scalar|Param|null,
 *                 pool?: scalar|Param|null,
 *             },
 *             entity_listeners?: array{
 *                 entities?: array<string, array{ // Default: []
 *                     listeners?: array<string, array{ // Default: []
 *                         events?: list<array{ // Default: []
 *                             type?: scalar|Param|null,
 *                             method?: scalar|Param|null, // Default: null
 *                         }>,
 *                     }>,
 *                 }>,
 *                 ...<string, mixed>
 *             },
 *             connection?: scalar|Param|null,
 *             class_metadata_factory_name?: scalar|Param|null, // Default: "Doctrine\\ORM\\Mapping\\ClassMetadataFactory"
 *             default_repository_class?: scalar|Param|null, // Default: "Doctrine\\ORM\\EntityRepository"
 *             auto_mapping?: scalar|Param|null, // Default: false
 *             naming_strategy?: scalar|Param|null, // Default: "doctrine.orm.naming_strategy.default"
 *             quote_strategy?: scalar|Param|null, // Default: "doctrine.orm.quote_strategy.default"
 *             typed_field_mapper?: scalar|Param|null, // Default: "doctrine.orm.typed_field_mapper.default"
 *             entity_listener_resolver?: scalar|Param|null, // Default: null
 *             fetch_mode_subselect_batch_size?: scalar|Param|null,
 *             repository_factory?: scalar|Param|null, // Default: "doctrine.orm.container_repository_factory"
 *             schema_ignore_classes?: list<scalar|Param|null>,
 *             validate_xml_mapping?: bool|Param, // Set to "true" to opt-in to the new mapping driver mode that was added in Doctrine ORM 2.14 and will be mandatory in ORM 3.0. See https://github.com/doctrine/orm/pull/6728. // Default: false
 *             second_level_cache?: array{
 *                 region_cache_driver?: Param|string|array{
 *                     type?: scalar|Param|null, // Default: null
 *                     id?: scalar|Param|null,
 *                     pool?: scalar|Param|null,
 *                 },
 *                 region_lock_lifetime?: scalar|Param|null, // Default: 60
 *                 log_enabled?: bool|Param, // Default: true
 *                 region_lifetime?: scalar|Param|null, // Default: 3600
 *                 enabled?: bool|Param, // Default: true
 *                 factory?: scalar|Param|null,
 *                 regions?: array<string, array{ // Default: []
 *                     cache_driver?: Param|string|array{
 *                         type?: scalar|Param|null, // Default: null
 *                         id?: scalar|Param|null,
 *                         pool?: scalar|Param|null,
 *                     },
 *                     lock_path?: scalar|Param|null, // Default: "%kernel.cache_dir%/doctrine/orm/slc/filelock"
 *                     lock_lifetime?: scalar|Param|null, // Default: 60
 *                     type?: scalar|Param|null, // Default: "default"
 *                     lifetime?: scalar|Param|null, // Default: null
 *                     service?: scalar|Param|null,
 *                     name?: scalar|Param|null,
 *                 }>,
 *                 loggers?: array<string, array{ // Default: []
 *                     name?: scalar|Param|null,
 *                     service?: scalar|Param|null,
 *                 }>,
 *             },
 *             hydrators?: array<string, scalar|Param|null>,
 *             mappings?: array<string, Param|bool|string|array{ // Default: []
 *                 mapping?: scalar|Param|null, // Default: true
 *                 type?: scalar|Param|null,
 *                 dir?: scalar|Param|null,
 *                 alias?: scalar|Param|null,
 *                 prefix?: scalar|Param|null,
 *                 is_bundle?: bool|Param,
 *             }>,
 *             dql?: array{
 *                 string_functions?: array<string, scalar|Param|null>,
 *                 numeric_functions?: array<string, scalar|Param|null>,
 *                 datetime_functions?: array<string, scalar|Param|null>,
 *             },
 *             filters?: array<string, Param|string|array{ // Default: []
 *                 class?: scalar|Param|null,
 *                 enabled?: bool|Param, // Default: false
 *                 parameters?: array<string, mixed>,
 *                 ...<string, mixed>
 *             }>,
 *             identity_generation_preferences?: array<string, scalar|Param|null>,
 *         }>,
 *         resolve_target_entities?: array<string, scalar|Param|null>,
 *         ...<string, mixed>
 *     },
 * }
 * @psalm-type DoctrineMigrationsConfig = array{
 *     enable_service_migrations?: bool|Param, // Whether to enable fetching migrations from the service container. // Default: false
 *     migrations_paths?: array<string, scalar|Param|null>,
 *     services?: array<string, scalar|Param|null>,
 *     factories?: array<string, scalar|Param|null>,
 *     storage?: array{ // Storage to use for migration status metadata.
 *         table_storage?: array{ // The default metadata storage, implemented as a table in the database.
 *             table_name?: scalar|Param|null, // Default: null
 *             version_column_name?: scalar|Param|null, // Default: null
 *             version_column_length?: scalar|Param|null, // Default: null
 *             executed_at_column_name?: scalar|Param|null, // Default: null
 *             execution_time_column_name?: scalar|Param|null, // Default: null
 *         },
 *     },
 *     migrations?: list<scalar|Param|null>,
 *     connection?: scalar|Param|null, // Connection name to use for the migrations database. // Default: null
 *     em?: scalar|Param|null, // Entity manager name to use for the migrations database (available when doctrine/orm is installed). // Default: null
 *     all_or_nothing?: scalar|Param|null, // Run all migrations in a transaction. // Default: false
 *     check_database_platform?: scalar|Param|null, // Adds an extra check in the generated migrations to allow execution only on the same platform as they were initially generated on. // Default: true
 *     custom_template?: scalar|Param|null, // Custom template path for generated migration classes. // Default: null
 *     organize_migrations?: scalar|Param|null, // Organize migrations mode. Possible values are: "BY_YEAR", "BY_YEAR_AND_MONTH", false // Default: false
 *     enable_profiler?: bool|Param, // Whether or not to enable the profiler collector to calculate and visualize migration status. This adds some queries overhead. // Default: false
 *     transactional?: bool|Param, // Whether or not to wrap migrations in a single transaction. // Default: true
 * }
 * @psalm-type DebugConfig = array{
 *     max_items?: int|Param, // Max number of displayed items past the first level, -1 means no limit. // Default: 2500
 *     min_depth?: int|Param, // Minimum tree depth to clone all the items, 1 is default. // Default: 1
 *     max_string_length?: int|Param, // Max length of displayed strings, -1 means no limit. // Default: -1
 *     dump_destination?: scalar|Param|null, // A stream URL where dumps should be written to. // Default: null
 *     theme?: "dark"|"light"|Param, // Changes the color of the dump() output when rendered directly on the templating. "dark" (default) or "light". // Default: "dark"
 * }
 * @psalm-type TwigConfig = array{
 *     form_themes?: list<scalar|Param|null>,
 *     globals?: array<string, array{ // Default: []
 *         id?: scalar|Param|null,
 *         type?: scalar|Param|null,
 *         value?: mixed,
 *         ...<string, mixed>
 *     }>,
 *     autoescape_service?: scalar|Param|null, // Default: null
 *     autoescape_service_method?: scalar|Param|null, // Default: null
 *     cache?: scalar|Param|null, // Default: true
 *     charset?: scalar|Param|null, // Default: "%kernel.charset%"
 *     debug?: bool|Param, // Default: "%kernel.debug%"
 *     strict_variables?: bool|Param, // Default: "%kernel.debug%"
 *     auto_reload?: scalar|Param|null,
 *     optimizations?: int|Param,
 *     default_path?: scalar|Param|null, // The default path used to load templates. // Default: "%kernel.project_dir%/templates"
 *     file_name_pattern?: Param|string|list<scalar|Param|null>,
 *     paths?: array<string, mixed>,
 *     date?: array{ // The default format options used by the date filter.
 *         format?: scalar|Param|null, // Default: "F j, Y H:i"
 *         interval_format?: scalar|Param|null, // Default: "%d days"
 *         timezone?: scalar|Param|null, // The timezone used when formatting dates, when set to null, the timezone returned by date_default_timezone_get() is used. // Default: null
 *     },
 *     number_format?: array{ // The default format options for the number_format filter.
 *         decimals?: int|Param, // Default: 0
 *         decimal_point?: scalar|Param|null, // Default: "."
 *         thousands_separator?: scalar|Param|null, // Default: ","
 *     },
 *     mailer?: array{
 *         html_to_text_converter?: scalar|Param|null, // A service implementing the "Symfony\Component\Mime\HtmlToTextConverter\HtmlToTextConverterInterface". // Default: null
 *     },
 * }
 * @psalm-type WebProfilerConfig = array{
 *     toolbar?: bool|array{ // Profiler toolbar configuration
 *         enabled?: bool|Param, // Default: false
 *         ajax_replace?: bool|Param, // Replace toolbar on AJAX requests // Default: false
 *     },
 *     intercept_redirects?: bool|Param, // Default: false
 *     excluded_ajax_paths?: scalar|Param|null, // Default: "^/((index|app(_[\\w]+)?)\\.php/)?_wdt"
 * }
 * @psalm-type TwigExtraConfig = array{
 *     cache?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     html?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     markdown?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     intl?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     cssinliner?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     inky?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     string?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     commonmark?: array{
 *         renderer?: array{ // Array of options for rendering HTML.
 *             block_separator?: scalar|Param|null,
 *             inner_separator?: scalar|Param|null,
 *             soft_break?: scalar|Param|null,
 *         },
 *         html_input?: "strip"|"allow"|"escape"|Param, // How to handle HTML input.
 *         allow_unsafe_links?: bool|Param, // Remove risky link and image URLs by setting this to false. // Default: true
 *         max_nesting_level?: int|Param, // The maximum nesting level for blocks. // Default: 9223372036854775807
 *         max_delimiters_per_line?: int|Param, // The maximum number of strong/emphasis delimiters per line. // Default: 9223372036854775807
 *         slug_normalizer?: array{ // Array of options for configuring how URL-safe slugs are created.
 *             instance?: mixed,
 *             max_length?: int|Param, // Default: 255
 *             unique?: mixed,
 *         },
 *         commonmark?: array{ // Array of options for configuring the CommonMark core extension.
 *             enable_em?: bool|Param, // Default: true
 *             enable_strong?: bool|Param, // Default: true
 *             use_asterisk?: bool|Param, // Default: true
 *             use_underscore?: bool|Param, // Default: true
 *             unordered_list_markers?: list<scalar|Param|null>,
 *         },
 *         ...<string, mixed>
 *     },
 * }
 * @psalm-type SecurityConfig = array{
 *     access_denied_url?: scalar|Param|null, // Default: null
 *     session_fixation_strategy?: "none"|"migrate"|"invalidate"|Param, // Default: "migrate"
 *     expose_security_errors?: \Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::None|\Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::AccountStatus|\Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel::All|Param, // Default: "none"
 *     erase_credentials?: bool|Param, // Deprecated: Setting the "security.erase_credentials.erase_credentials" configuration option is deprecated. It will be removed in Symfony 9.0, as the "eraseCredentials()" method was removed in Symfony 8.0. // Default: true
 *     access_decision_manager?: array{
 *         strategy?: "affirmative"|"consensus"|"unanimous"|"priority"|Param,
 *         service?: scalar|Param|null,
 *         strategy_service?: scalar|Param|null,
 *         allow_if_all_abstain?: bool|Param, // Default: false
 *         allow_if_equal_granted_denied?: bool|Param, // Default: true
 *     },
 *     password_hashers?: array<string, Param|string|array{ // Default: []
 *         algorithm?: scalar|Param|null,
 *         migrate_from?: Param|string|list<scalar|Param|null>,
 *         hash_algorithm?: scalar|Param|null, // Name of hashing algorithm for PBKDF2 (i.e. sha256, sha512, etc..) See hash_algos() for a list of supported algorithms. // Default: "sha512"
 *         key_length?: scalar|Param|null, // Default: 40
 *         ignore_case?: bool|Param, // Default: false
 *         encode_as_base64?: bool|Param, // Default: true
 *         iterations?: scalar|Param|null, // Default: 5000
 *         cost?: int|Param, // Default: null
 *         memory_cost?: scalar|Param|null, // Default: null
 *         time_cost?: scalar|Param|null, // Default: null
 *         id?: scalar|Param|null,
 *     }>,
 *     providers?: array<string, array{ // Default: []
 *         id?: scalar|Param|null,
 *         chain?: array{
 *             providers?: Param|string|list<scalar|Param|null>,
 *         },
 *         entity?: array{
 *             class?: scalar|Param|null, // The full entity class name of your user class.
 *             property?: scalar|Param|null, // Default: null
 *             manager_name?: scalar|Param|null, // Default: null
 *         },
 *         memory?: array{
 *             users?: array<string, array{ // Default: []
 *                 password?: scalar|Param|null, // Default: null
 *                 roles?: Param|string|list<scalar|Param|null>,
 *             }>,
 *         },
 *         ldap?: array{
 *             service?: scalar|Param|null,
 *             base_dn?: scalar|Param|null,
 *             search_dn?: scalar|Param|null, // Default: null
 *             search_password?: scalar|Param|null, // Default: null
 *             extra_fields?: list<scalar|Param|null>,
 *             default_roles?: Param|string|list<scalar|Param|null>,
 *             role_fetcher?: scalar|Param|null, // Default: null
 *             uid_key?: scalar|Param|null, // Default: "sAMAccountName"
 *             filter?: scalar|Param|null, // Default: "({uid_key}={user_identifier})"
 *             password_attribute?: scalar|Param|null, // Default: null
 *         },
 *     }>,
 *     firewalls?: array<string, array{ // Default: []
 *         pattern?: scalar|Param|null,
 *         host?: scalar|Param|null,
 *         methods?: Param|string|list<scalar|Param|null>,
 *         security?: bool|Param, // Default: true
 *         user_checker?: scalar|Param|null, // The UserChecker to use when authenticating users in this firewall. // Default: "security.user_checker"
 *         request_matcher?: scalar|Param|null,
 *         access_denied_url?: scalar|Param|null,
 *         access_denied_handler?: scalar|Param|null,
 *         entry_point?: scalar|Param|null, // An enabled authenticator name or a service id that implements "Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface".
 *         provider?: scalar|Param|null,
 *         stateless?: bool|Param, // Default: false
 *         lazy?: bool|Param, // Default: false
 *         context?: scalar|Param|null,
 *         logout?: array{
 *             enable_csrf?: bool|Param|null, // Default: null
 *             csrf_token_id?: scalar|Param|null, // Default: "logout"
 *             csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *             csrf_token_manager?: scalar|Param|null,
 *             path?: scalar|Param|null, // Default: "/logout"
 *             target?: scalar|Param|null, // Default: "/"
 *             invalidate_session?: bool|Param, // Default: true
 *             clear_site_data?: Param|string|list<"*"|"cache"|"cookies"|"storage"|"clientHints"|"executionContexts"|"prefetchCache"|"prerenderCache"|Param>,
 *             delete_cookies?: Param|string|array<string, array{ // Default: []
 *                 path?: scalar|Param|null, // Default: null
 *                 domain?: scalar|Param|null, // Default: null
 *                 secure?: scalar|Param|null, // Default: false
 *                 samesite?: scalar|Param|null, // Default: null
 *                 partitioned?: scalar|Param|null, // Default: false
 *             }>,
 *         },
 *         switch_user?: array{
 *             provider?: scalar|Param|null,
 *             parameter?: scalar|Param|null, // Default: "_switch_user"
 *             role?: scalar|Param|null, // Default: "ROLE_ALLOWED_TO_SWITCH"
 *             target_route?: scalar|Param|null, // Default: null
 *         },
 *         required_badges?: list<scalar|Param|null>,
 *         custom_authenticators?: list<scalar|Param|null>,
 *         login_throttling?: array{
 *             limiter?: scalar|Param|null, // A service id implementing "Symfony\Component\HttpFoundation\RateLimiter\RequestRateLimiterInterface".
 *             max_attempts?: int|Param, // Default: 5
 *             interval?: scalar|Param|null, // Default: "1 minute"
 *             lock_factory?: scalar|Param|null, // The service ID of the lock factory used by the login rate limiter (or null to disable locking). // Default: null
 *             cache_pool?: string|Param, // The cache pool to use for storing the limiter state // Default: "cache.rate_limiter"
 *             storage_service?: string|Param, // The service ID of a custom storage implementation, this precedes any configured "cache_pool" // Default: null
 *         },
 *         x509?: array{
 *             provider?: scalar|Param|null,
 *             user?: scalar|Param|null, // Default: "SSL_CLIENT_S_DN_Email"
 *             credentials?: scalar|Param|null, // Default: "SSL_CLIENT_S_DN"
 *             user_identifier?: scalar|Param|null, // Default: "emailAddress"
 *         },
 *         remote_user?: array{
 *             provider?: scalar|Param|null,
 *             user?: scalar|Param|null, // Default: "REMOTE_USER"
 *         },
 *         login_link?: array{
 *             check_route?: scalar|Param|null, // Route that will validate the login link - e.g. "app_login_link_verify".
 *             check_post_only?: scalar|Param|null, // If true, only HTTP POST requests to "check_route" will be handled by the authenticator. // Default: false
 *             signature_properties?: list<scalar|Param|null>,
 *             lifetime?: int|Param, // The lifetime of the login link in seconds. // Default: 600
 *             max_uses?: int|Param, // Max number of times a login link can be used - null means unlimited within lifetime. // Default: null
 *             used_link_cache?: scalar|Param|null, // Cache service id used to expired links of max_uses is set.
 *             success_handler?: scalar|Param|null, // A service id that implements Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface.
 *             failure_handler?: scalar|Param|null, // A service id that implements Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface.
 *             provider?: scalar|Param|null, // The user provider to load users from.
 *             secret?: scalar|Param|null, // Default: "%kernel.secret%"
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|Param|null, // Default: "/"
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|Param|null, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *         },
 *         form_login?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             check_path?: scalar|Param|null, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             username_parameter?: scalar|Param|null, // Default: "_username"
 *             password_parameter?: scalar|Param|null, // Default: "_password"
 *             csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *             csrf_token_id?: scalar|Param|null, // Default: "authenticate"
 *             enable_csrf?: bool|Param, // Default: false
 *             post_only?: bool|Param, // Default: true
 *             form_only?: bool|Param, // Default: false
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|Param|null, // Default: "/"
 *             target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|Param|null, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *         },
 *         form_login_ldap?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             check_path?: scalar|Param|null, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             username_parameter?: scalar|Param|null, // Default: "_username"
 *             password_parameter?: scalar|Param|null, // Default: "_password"
 *             csrf_parameter?: scalar|Param|null, // Default: "_csrf_token"
 *             csrf_token_id?: scalar|Param|null, // Default: "authenticate"
 *             enable_csrf?: bool|Param, // Default: false
 *             post_only?: bool|Param, // Default: true
 *             form_only?: bool|Param, // Default: false
 *             always_use_default_target_path?: bool|Param, // Default: false
 *             default_target_path?: scalar|Param|null, // Default: "/"
 *             target_path_parameter?: scalar|Param|null, // Default: "_target_path"
 *             use_referer?: bool|Param, // Default: false
 *             failure_path?: scalar|Param|null, // Default: null
 *             failure_forward?: bool|Param, // Default: false
 *             failure_path_parameter?: scalar|Param|null, // Default: "_failure_path"
 *             service?: scalar|Param|null, // Default: "ldap"
 *             dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *             query_string?: scalar|Param|null,
 *             search_dn?: scalar|Param|null, // Default: ""
 *             search_password?: scalar|Param|null, // Default: ""
 *         },
 *         json_login?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             check_path?: scalar|Param|null, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             username_path?: scalar|Param|null, // Default: "username"
 *             password_path?: scalar|Param|null, // Default: "password"
 *         },
 *         json_login_ldap?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             check_path?: scalar|Param|null, // Default: "/login_check"
 *             use_forward?: bool|Param, // Default: false
 *             login_path?: scalar|Param|null, // Default: "/login"
 *             username_path?: scalar|Param|null, // Default: "username"
 *             password_path?: scalar|Param|null, // Default: "password"
 *             service?: scalar|Param|null, // Default: "ldap"
 *             dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *             query_string?: scalar|Param|null,
 *             search_dn?: scalar|Param|null, // Default: ""
 *             search_password?: scalar|Param|null, // Default: ""
 *         },
 *         access_token?: array{
 *             provider?: scalar|Param|null,
 *             remember_me?: bool|Param, // Default: true
 *             success_handler?: scalar|Param|null,
 *             failure_handler?: scalar|Param|null,
 *             realm?: scalar|Param|null, // Default: null
 *             token_extractors?: Param|string|list<scalar|Param|null>,
 *             token_handler?: Param|string|array{
 *                 id?: scalar|Param|null,
 *                 oidc_user_info?: Param|string|array{
 *                     base_uri?: scalar|Param|null, // Base URI of the userinfo endpoint on the OIDC server, or the OIDC server URI to use the discovery (require "discovery" to be configured).
 *                     discovery?: array{ // Enable the OIDC discovery.
 *                         cache?: array{
 *                             id?: scalar|Param|null, // Cache service id to use to cache the OIDC discovery configuration.
 *                         },
 *                     },
 *                     claim?: scalar|Param|null, // Claim which contains the user identifier (e.g. sub, email, etc.). // Default: "sub"
 *                     client?: scalar|Param|null, // HttpClient service id to use to call the OIDC server.
 *                 },
 *                 oidc?: array{
 *                     discovery?: array{ // Enable the OIDC discovery.
 *                         base_uri?: Param|string|list<scalar|Param|null>,
 *                         cache?: array{
 *                             id?: scalar|Param|null, // Cache service id to use to cache the OIDC discovery configuration.
 *                         },
 *                         enforce_key_usage_verification?: bool|Param, // When enabled (default), only keys explicitly designated for signature (via "use":"sig" or a "key_ops" entry containing "sign"/"verify") are accepted. When disabled, keys without any usage designation are also accepted; keys explicitly restricted to encryption are still rejected. // Default: true
 *                     },
 *                     claim?: scalar|Param|null, // Claim which contains the user identifier (e.g.: sub, email..). // Default: "sub"
 *                     audience?: scalar|Param|null, // Audience set in the token, for validation purpose.
 *                     issuers?: list<scalar|Param|null>,
 *                     algorithms?: list<scalar|Param|null>,
 *                     keyset?: scalar|Param|null, // JSON-encoded JWKSet used to sign the token (must contain a list of valid public keys).
 *                     encryption?: bool|array{
 *                         enabled?: bool|Param, // Default: false
 *                         enforce?: bool|Param, // When enabled, the token shall be encrypted. // Default: false
 *                         algorithms?: list<scalar|Param|null>,
 *                         keyset?: scalar|Param|null, // JSON-encoded JWKSet used to decrypt the token (must contain a list of valid private keys).
 *                     },
 *                 },
 *                 cas?: array{
 *                     validation_url?: scalar|Param|null, // CAS server validation URL
 *                     prefix?: scalar|Param|null, // CAS prefix // Default: "cas"
 *                     http_client?: scalar|Param|null, // HTTP Client service // Default: null
 *                 },
 *                 oauth2?: scalar|Param|null,
 *             },
 *         },
 *         http_basic?: array{
 *             provider?: scalar|Param|null,
 *             realm?: scalar|Param|null, // Default: "Secured Area"
 *         },
 *         http_basic_ldap?: array{
 *             provider?: scalar|Param|null,
 *             realm?: scalar|Param|null, // Default: "Secured Area"
 *             service?: scalar|Param|null, // Default: "ldap"
 *             dn_string?: scalar|Param|null, // Default: "{user_identifier}"
 *             query_string?: scalar|Param|null,
 *             search_dn?: scalar|Param|null, // Default: ""
 *             search_password?: scalar|Param|null, // Default: ""
 *         },
 *         remember_me?: array{
 *             secret?: scalar|Param|null, // Default: "%kernel.secret%"
 *             service?: scalar|Param|null,
 *             user_providers?: Param|string|list<scalar|Param|null>,
 *             catch_exceptions?: bool|Param, // Default: true
 *             signature_properties?: list<scalar|Param|null>,
 *             token_provider?: Param|string|array{
 *                 service?: scalar|Param|null, // The service ID of a custom remember-me token provider.
 *                 doctrine?: bool|array{
 *                     enabled?: bool|Param, // Default: false
 *                     connection?: scalar|Param|null, // Default: null
 *                 },
 *             },
 *             token_verifier?: scalar|Param|null, // The service ID of a custom rememberme token verifier.
 *             name?: scalar|Param|null, // Default: "REMEMBERME"
 *             lifetime?: int|Param, // Default: 31536000
 *             path?: scalar|Param|null, // Default: "/"
 *             domain?: scalar|Param|null, // Default: null
 *             secure?: true|false|"auto"|Param, // Default: null
 *             httponly?: bool|Param, // Default: true
 *             samesite?: null|"lax"|"strict"|"none"|Param, // Default: "lax"
 *             always_remember_me?: bool|Param, // Default: false
 *             remember_me_parameter?: scalar|Param|null, // Default: "_remember_me"
 *         },
 *     }>,
 *     access_control?: list<array{ // Default: []
 *         request_matcher?: scalar|Param|null, // Default: null
 *         requires_channel?: scalar|Param|null, // Default: null
 *         path?: scalar|Param|null, // Use the urldecoded format. // Default: null
 *         host?: scalar|Param|null, // Default: null
 *         port?: int|Param, // Default: null
 *         ips?: Param|string|list<scalar|Param|null>,
 *         attributes?: array<string, scalar|Param|null>,
 *         route?: scalar|Param|null, // Default: null
 *         methods?: Param|string|list<scalar|Param|null>,
 *         allow_if?: scalar|Param|null, // Default: null
 *         roles?: Param|string|list<scalar|Param|null>,
 *     }>,
 *     role_hierarchy?: array<string, Param|string|list<scalar|Param|null>>,
 * }
 * @psalm-type MonologConfig = array{
 *     use_microseconds?: scalar|Param|null, // Default: true
 *     channels?: list<scalar|Param|null>,
 *     handlers?: array<string, array{ // Default: []
 *         type?: scalar|Param|null,
 *         id?: scalar|Param|null,
 *         enabled?: bool|Param, // Default: true
 *         priority?: scalar|Param|null, // Default: 0
 *         level?: scalar|Param|null, // Default: "DEBUG"
 *         bubble?: bool|Param, // Default: true
 *         interactive_only?: bool|Param, // Default: false
 *         app_name?: scalar|Param|null, // Default: null
 *         include_stacktraces?: bool|Param, // Default: false
 *         process_psr_3_messages?: array{
 *             enabled?: bool|Param|null, // Default: null
 *             date_format?: scalar|Param|null,
 *             remove_used_context_fields?: bool|Param,
 *             ...<string, mixed>
 *         },
 *         path?: scalar|Param|null, // Default: "%kernel.logs_dir%/%kernel.environment%.log"
 *         file_permission?: scalar|Param|null, // Default: null
 *         use_locking?: bool|Param, // Default: false
 *         filename_format?: scalar|Param|null, // Default: "{filename}-{date}"
 *         date_format?: scalar|Param|null, // Default: "Y-m-d"
 *         ident?: scalar|Param|null, // Default: false
 *         logopts?: scalar|Param|null, // Default: 1
 *         facility?: scalar|Param|null, // Default: "user"
 *         max_files?: scalar|Param|null, // Default: 0
 *         action_level?: scalar|Param|null, // Default: "WARNING"
 *         activation_strategy?: scalar|Param|null, // Default: null
 *         stop_buffering?: bool|Param, // Default: true
 *         passthru_level?: scalar|Param|null, // Default: null
 *         excluded_http_codes?: list<array{ // Default: []
 *             code?: scalar|Param|null,
 *             urls?: list<scalar|Param|null>,
 *         }>,
 *         accepted_levels?: list<scalar|Param|null>,
 *         min_level?: scalar|Param|null, // Default: "DEBUG"
 *         max_level?: scalar|Param|null, // Default: "EMERGENCY"
 *         buffer_size?: scalar|Param|null, // Default: 0
 *         flush_on_overflow?: bool|Param, // Default: false
 *         handler?: scalar|Param|null,
 *         url?: scalar|Param|null,
 *         exchange?: scalar|Param|null,
 *         exchange_name?: scalar|Param|null, // Default: "log"
 *         channel?: scalar|Param|null, // Default: null
 *         bot_name?: scalar|Param|null, // Default: "Monolog"
 *         use_attachment?: scalar|Param|null, // Default: true
 *         use_short_attachment?: scalar|Param|null, // Default: false
 *         include_extra?: scalar|Param|null, // Default: false
 *         icon_emoji?: scalar|Param|null, // Default: null
 *         webhook_url?: scalar|Param|null,
 *         exclude_fields?: list<scalar|Param|null>,
 *         token?: scalar|Param|null,
 *         region?: scalar|Param|null,
 *         source?: scalar|Param|null,
 *         use_ssl?: bool|Param, // Default: true
 *         user?: mixed,
 *         title?: scalar|Param|null, // Default: null
 *         host?: scalar|Param|null, // Default: null
 *         port?: scalar|Param|null, // Default: 514
 *         config?: list<scalar|Param|null>,
 *         members?: list<scalar|Param|null>,
 *         connection_string?: scalar|Param|null,
 *         timeout?: scalar|Param|null,
 *         time?: scalar|Param|null, // Default: 60
 *         deduplication_level?: scalar|Param|null, // Default: 400
 *         store?: scalar|Param|null, // Default: null
 *         connection_timeout?: scalar|Param|null,
 *         persistent?: bool|Param,
 *         message_type?: scalar|Param|null, // Default: 0
 *         parse_mode?: scalar|Param|null, // Default: null
 *         disable_webpage_preview?: bool|Param|null, // Default: null
 *         disable_notification?: bool|Param|null, // Default: null
 *         split_long_messages?: bool|Param, // Default: false
 *         delay_between_messages?: bool|Param, // Default: false
 *         topic?: int|Param, // Default: null
 *         factor?: int|Param, // Default: 1
 *         tags?: Param|string|list<scalar|Param|null>,
 *         console_formatter_options?: mixed, // Default: []
 *         formatter?: scalar|Param|null,
 *         nested?: bool|Param, // Default: false
 *         publisher?: Param|string|array{
 *             id?: scalar|Param|null,
 *             hostname?: scalar|Param|null,
 *             port?: scalar|Param|null, // Default: 12201
 *             chunk_size?: scalar|Param|null, // Default: 1420
 *             encoder?: "json"|"compressed_json"|Param,
 *         },
 *         mongodb?: Param|string|array{
 *             id?: scalar|Param|null, // ID of a MongoDB\Client service
 *             uri?: scalar|Param|null,
 *             username?: scalar|Param|null,
 *             password?: scalar|Param|null,
 *             database?: scalar|Param|null, // Default: "monolog"
 *             collection?: scalar|Param|null, // Default: "logs"
 *         },
 *         elasticsearch?: Param|string|array{
 *             id?: scalar|Param|null,
 *             hosts?: list<scalar|Param|null>,
 *             host?: scalar|Param|null,
 *             port?: scalar|Param|null, // Default: 9200
 *             transport?: scalar|Param|null, // Default: "Http"
 *             user?: scalar|Param|null, // Default: null
 *             password?: scalar|Param|null, // Default: null
 *         },
 *         index?: scalar|Param|null, // Default: "monolog"
 *         document_type?: scalar|Param|null, // Default: "logs"
 *         ignore_error?: scalar|Param|null, // Default: false
 *         redis?: Param|string|array{
 *             id?: scalar|Param|null,
 *             host?: scalar|Param|null,
 *             password?: scalar|Param|null, // Default: null
 *             port?: scalar|Param|null, // Default: 6379
 *             database?: scalar|Param|null, // Default: 0
 *             key_name?: scalar|Param|null, // Default: "monolog_redis"
 *         },
 *         predis?: Param|string|array{
 *             id?: scalar|Param|null,
 *             host?: scalar|Param|null,
 *         },
 *         from_email?: scalar|Param|null,
 *         to_email?: Param|string|list<scalar|Param|null>,
 *         subject?: scalar|Param|null,
 *         content_type?: scalar|Param|null, // Default: null
 *         headers?: list<scalar|Param|null>,
 *         mailer?: scalar|Param|null, // Default: null
 *         email_prototype?: Param|string|array{
 *             id?: scalar|Param|null,
 *             method?: scalar|Param|null, // Default: null
 *         },
 *         verbosity_levels?: array{
 *             VERBOSITY_QUIET?: scalar|Param|null, // Default: "ERROR"
 *             VERBOSITY_NORMAL?: scalar|Param|null, // Default: "WARNING"
 *             VERBOSITY_VERBOSE?: scalar|Param|null, // Default: "NOTICE"
 *             VERBOSITY_VERY_VERBOSE?: scalar|Param|null, // Default: "INFO"
 *             VERBOSITY_DEBUG?: scalar|Param|null, // Default: "DEBUG"
 *         },
 *         channels?: Param|string|array{
 *             type?: scalar|Param|null,
 *             elements?: list<scalar|Param|null>,
 *             ...<string, mixed>
 *         },
 *     }>,
 * }
 * @psalm-type MakerConfig = array{
 *     root_namespace?: scalar|Param|null, // Default: "App"
 *     generate_final_classes?: bool|Param, // Default: true
 *     generate_final_entities?: bool|Param, // Default: false
 * }
 * @psalm-type StimulusConfig = array{
 *     controller_paths?: list<scalar|Param|null>,
 *     controllers_json?: scalar|Param|null, // Default: "%kernel.project_dir%/assets/controllers.json"
 * }
 * @psalm-type TwigComponentConfig = array{
 *     defaults?: array<string, Param|string|array{ // Default: []
 *         template_directory?: scalar|Param|null, // Default: "components"
 *         name_prefix?: scalar|Param|null, // Default: ""
 *     }>,
 *     anonymous_template_directory?: scalar|Param|null, // Defaults to `components`
 *     profiler?: bool|array{ // Enables the profiler for Twig Component
 *         enabled?: bool|Param, // Default: "%kernel.debug%"
 *         collect_components?: bool|Param, // Collect components instances // Default: true
 *     },
 * }
 * @psalm-type SurvosCoreConfig = array{
 *     enabled?: bool|Param, // Default: true
 *     dd?: bool|Param, // Default: true
 * }
 * @psalm-type KnpMenuConfig = array{
 *     providers?: array{
 *         builder_alias?: bool|Param, // Default: true
 *     },
 *     twig?: array{
 *         template?: scalar|Param|null, // Default: "@KnpMenu/menu.html.twig"
 *     },
 *     templating?: bool|Param, // Default: false
 *     default_renderer?: scalar|Param|null, // Default: "twig"
 * }
 * @psalm-type SurvosHtmlPrettifyConfig = array{
 *     indentation_character?: scalar|Param|null, // Default: " "
 * }
 * @psalm-type NelmioCorsConfig = array{
 *     defaults?: array{
 *         allow_credentials?: bool|Param, // Default: false
 *         allow_origin?: list<scalar|Param|null>,
 *         allow_headers?: list<scalar|Param|null>,
 *         allow_methods?: list<scalar|Param|null>,
 *         allow_private_network?: bool|Param, // Default: false
 *         expose_headers?: list<scalar|Param|null>,
 *         max_age?: scalar|Param|null, // Default: 0
 *         hosts?: list<scalar|Param|null>,
 *         origin_regex?: bool|Param, // Default: false
 *         forced_allow_origin_value?: scalar|Param|null, // Default: null
 *         skip_same_as_origin?: bool|Param, // Default: true
 *     },
 *     paths?: array<string, array{ // Default: []
 *         allow_credentials?: bool|Param,
 *         allow_origin?: list<scalar|Param|null>,
 *         allow_headers?: list<scalar|Param|null>,
 *         allow_methods?: list<scalar|Param|null>,
 *         allow_private_network?: bool|Param,
 *         expose_headers?: list<scalar|Param|null>,
 *         max_age?: scalar|Param|null, // Default: 0
 *         hosts?: list<scalar|Param|null>,
 *         origin_regex?: bool|Param,
 *         forced_allow_origin_value?: scalar|Param|null, // Default: null
 *         skip_same_as_origin?: bool|Param,
 *     }>,
 * }
 * @psalm-type KnpuOauth2ClientConfig = array{
 *     http_client?: scalar|Param|null, // Service id of HTTP client to use (must implement GuzzleHttp\ClientInterface) // Default: null
 *     http_client_options?: array{
 *         timeout?: int|Param,
 *         proxy?: scalar|Param|null,
 *         verify?: bool|Param, // Use only with proxy option set
 *     },
 *     clients?: array<string, array<string, mixed>>,
 * }
 * @psalm-type SurvosAuthConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: "/auth"
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     providers?: array<string, array{ // Default: []
 *         type?: scalar|Param|null, // Default: null
 *         client_id?: scalar|Param|null, // Default: null
 *         client_secret?: scalar|Param|null, // Default: null
 *         scopes?: list<scalar|Param|null>,
 *         redirect_route?: scalar|Param|null, // Default: null
 *         redirect_params?: list<scalar|Param|null>,
 *         use_state?: bool|Param|null, // Default: null
 *     }>,
 *     new_user_redirect_route?: scalar|Param|null, // Default: "oauth_profile"
 *     production_url_base?: scalar|Param|null, // Default: null
 *     user_provider?: scalar|Param|null, // Default: null
 *     user_class?: scalar|Param|null, // Default: "App\\Entity\\User"
 * }
 * @psalm-type SurvosSimpleDatatablesConfig = array{
 *     stimulus_controller?: scalar|Param|null, // Default: "@survos/simple-datatables-bundle/table"
 *     per_page?: bool|Param, // Default: 10
 *     searchable?: bool|Param, // Default: true
 *     fixed_height?: scalar|Param|null, // Default: true
 * }
 * @psalm-type SentryConfig = array{
 *     dsn?: scalar|Param|null, // If this value is not provided, the SDK will try to read it from the SENTRY_DSN environment variable. If that variable also does not exist, the SDK will not send any events.
 *     register_error_listener?: bool|Param, // Default: true
 *     register_error_handler?: bool|Param, // Default: true
 *     logger?: scalar|Param|null, // The service ID of the PSR-3 logger used to log messages coming from the SDK client. Be aware that setting the same logger of the application may create a circular loop when an event fails to be sent. // Default: null
 *     options?: array{
 *         integrations?: mixed, // Default: []
 *         default_integrations?: bool|Param,
 *         prefixes?: list<scalar|Param|null>,
 *         sample_rate?: float|Param, // The sampling factor to apply to events. A value of 0 will deny sending any event, and a value of 1 will send all events.
 *         enable_tracing?: bool|Param,
 *         traces_sample_rate?: float|Param, // The sampling factor to apply to transactions. A value of 0 will deny sending any transaction, and a value of 1 will send all transactions.
 *         traces_sampler?: scalar|Param|null,
 *         profiles_sample_rate?: float|Param, // The sampling factor to apply to profiles. A value of 0 will deny sending any profiles, and a value of 1 will send all profiles. Profiles are sampled in relation to traces_sample_rate
 *         enable_logs?: bool|Param,
 *         log_flush_threshold?: mixed, // Default: null
 *         enable_metrics?: bool|Param, // Default: true
 *         attach_stacktrace?: bool|Param,
 *         attach_metric_code_locations?: bool|Param,
 *         context_lines?: int|Param,
 *         environment?: scalar|Param|null, // Default: "%kernel.environment%"
 *         logger?: scalar|Param|null,
 *         spotlight?: bool|Param,
 *         spotlight_url?: scalar|Param|null,
 *         release?: scalar|Param|null, // Default: "%env(default::SENTRY_RELEASE)%"
 *         org_id?: int|Param,
 *         server_name?: scalar|Param|null,
 *         ignore_exceptions?: list<scalar|Param|null>,
 *         ignore_transactions?: list<scalar|Param|null>,
 *         before_send?: scalar|Param|null,
 *         before_send_transaction?: scalar|Param|null,
 *         before_send_check_in?: scalar|Param|null,
 *         before_send_metrics?: scalar|Param|null,
 *         before_send_log?: scalar|Param|null,
 *         before_send_metric?: scalar|Param|null,
 *         trace_propagation_targets?: mixed,
 *         strict_trace_continuation?: bool|Param,
 *         tags?: array<string, scalar|Param|null>,
 *         error_types?: scalar|Param|null,
 *         max_breadcrumbs?: int|Param,
 *         before_breadcrumb?: mixed,
 *         in_app_exclude?: list<scalar|Param|null>,
 *         in_app_include?: list<scalar|Param|null>,
 *         send_default_pii?: bool|Param,
 *         max_value_length?: int|Param,
 *         transport?: scalar|Param|null,
 *         http_client?: scalar|Param|null,
 *         http_proxy?: scalar|Param|null,
 *         http_proxy_authentication?: scalar|Param|null,
 *         http_connect_timeout?: float|Param, // The maximum number of seconds to wait while trying to connect to a server. It works only when using the default transport.
 *         http_timeout?: float|Param, // The maximum execution time for the request+response as a whole. It works only when using the default transport.
 *         http_ssl_verify_peer?: bool|Param,
 *         http_compression?: bool|Param,
 *         capture_silenced_errors?: bool|Param,
 *         max_request_body_size?: "none"|"never"|"small"|"medium"|"always"|Param,
 *         class_serializers?: array<string, scalar|Param|null>,
 *     },
 *     messenger?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         capture_soft_fails?: bool|Param, // Default: true
 *         isolate_breadcrumbs_by_message?: bool|Param, // Default: false
 *         isolate_context_by_message?: bool|Param, // Default: false
 *     },
 *     tracing?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         dbal?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *             ignore_prepare_spans?: bool|Param, // Default: false
 *             connections?: list<scalar|Param|null>,
 *         },
 *         twig?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *         },
 *         cache?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *         },
 *         http_client?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *         },
 *         console?: array{
 *             excluded_commands?: list<scalar|Param|null>,
 *         },
 *     },
 * }
 * @psalm-type FosJsRoutingConfig = array{
 *     serializer?: scalar|Param|null,
 *     routes_to_expose?: list<scalar|Param|null>,
 *     router?: scalar|Param|null, // Default: "router"
 *     request_context_base_url?: scalar|Param|null, // Default: null
 *     cache_control?: array{
 *         public?: bool|Param, // Default: false
 *         expires?: scalar|Param|null, // Default: null
 *         maxage?: scalar|Param|null, // Default: null
 *         smaxage?: scalar|Param|null, // Default: null
 *         vary?: list<scalar|Param|null>,
 *     },
 * }
 * @psalm-type SurvosCrawlerConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: "/crawler"
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     users?: list<mixed>,
 *     routes_to_ignore?: list<mixed>,
 *     paths_to_ignore?: list<mixed>,
 *     max_per_route?: scalar|Param|null, // Default: 3
 *     base_url?: scalar|Param|null, // Default: "https://127.0.0.1:8000"
 *     initial_path?: scalar|Param|null, // Default: "/"
 *     user?: scalar|Param|null, // Default: "juan@tt.com"
 *     login_path?: scalar|Param|null, // Default: "/login"
 *     username_form_variable?: scalar|Param|null, // Default: "_username"
 *     password_form_variable?: scalar|Param|null, // Default: "_password"
 *     plaintext_password?: scalar|Param|null, // Default: "password"
 *     submit_button?: scalar|Param|null, // Default: ".btn"
 *     user_class?: scalar|Param|null, // Default: "App\\Entity\\User"
 *     max_depth?: scalar|Param|null, // Default: 1
 * }
 * @psalm-type SurvosCommandConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: false
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: "/admin/commands"
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     base_layout?: scalar|Param|null, // Default: null
 *     subdomain_variable?: scalar|Param|null, // Default: "subdomain"
 *     track?: bool|Param, // Record each (namespaced) command run as a CommandProcess row for monitoring. // Default: true
 *     namespaces?: list<scalar|Param|null>,
 * }
 * @psalm-type SurvosWikiConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: "/wiki"
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     search_limit?: int|Param, // Default: 20
 *     cache_timeout?: int|Param, // Default: 86400
 *     enabled?: bool|Param, // Default: true
 *     properties?: array<string, scalar|Param|null>,
 * }
 * @psalm-type FlysystemConfig = array{
 *     storages?: array<string, array{ // Default: []
 *         adapter?: scalar|Param|null, // DEPRECATED: Use the new config format instead (e.g. "local:" instead of "adapter: local")
 *         options?: list<mixed>,
 *         asyncaws?: array{
 *             client?: scalar|Param|null, // The AsyncAws S3 client service name
 *             bucket?: scalar|Param|null, // The name of the AWS S3 bucket
 *             prefix?: scalar|Param|null, // Optional path prefix to prepend to all object keys // Default: ""
 *         },
 *         aws?: array{
 *             client?: scalar|Param|null, // The AWS S3 client service name
 *             bucket?: scalar|Param|null, // The name of the AWS S3 bucket
 *             prefix?: scalar|Param|null, // Optional path prefix to prepend to all object keys // Default: ""
 *             options?: list<mixed>,
 *             streamReads?: bool|Param, // Whether to use streaming for file reads // Default: true
 *         },
 *         azure?: array{
 *             client?: scalar|Param|null, // The Azure Blob Storage client service name
 *             container?: scalar|Param|null, // The name of the Azure Blob Storage container
 *             prefix?: scalar|Param|null, // Optional path prefix to prepend to all blob names // Default: ""
 *         },
 *         ftp?: array{
 *             host?: scalar|Param|null, // FTP host
 *             username?: scalar|Param|null, // FTP username
 *             password?: scalar|Param|null, // FTP password
 *             port?: int|Param, // FTP port number // Default: 21
 *             root?: scalar|Param|null, // FTP root directory // Default: ""
 *             passive?: bool|Param, // Use passive mode // Default: true
 *             ssl?: bool|Param, // Use SSL/TLS encryption // Default: false
 *             timeout?: int|Param, // Connection timeout in seconds // Default: 90
 *             ignore_passive_address?: scalar|Param|null, // Ignore passive address // Default: null
 *             utf8?: bool|Param, // Enable UTF8 mode // Default: false
 *             transfer_mode?: scalar|Param|null, // Transfer mode (FTP_ASCII or FTP_BINARY constante on ftp extension) // Default: null
 *             system_type?: null|"windows"|"unix"|Param, // FTP system type // Default: null
 *             timestamps_on_unix_listings_enabled?: bool|Param, // Enable timestamps on Unix listings // Default: false
 *             recurse_manually?: bool|Param, // Recurse directories manually // Default: true
 *             use_raw_list_options?: bool|Param|null, // Use raw list options // Default: null
 *             connectivityChecker?: scalar|Param|null, // Connectivity checker service name // Default: null
 *             permissions?: array{ // Unix permissions configuration for files and directories
 *                 file?: array{ // File permissions
 *                     public?: int|Param, // Public file permissions // Default: 420
 *                     private?: int|Param, // Private file permissions // Default: 384
 *                 },
 *                 dir?: array{ // Directory permissions
 *                     public?: int|Param, // Public directory permissions // Default: 493
 *                     private?: int|Param, // Private directory permissions // Default: 448
 *                 },
 *             },
 *         },
 *         gcloud?: array{
 *             client?: scalar|Param|null, // The Google Cloud Storage client service name
 *             bucket?: scalar|Param|null, // The name of the Google Cloud Storage bucket
 *             prefix?: scalar|Param|null, // Optional path prefix to prepend to all object keys // Default: ""
 *             visibility_handler?: scalar|Param|null, // Optional visibility handler service name // Default: null
 *             streamReads?: bool|Param, // Whether to use streaming for file reads // Default: false
 *         },
 *         gridfs?: array{
 *             bucket?: scalar|Param|null, // GridFS bucket service name (if using an existing bucket service) // Default: null
 *             prefix?: scalar|Param|null, // Optional path prefix to prepend to all file names // Default: ""
 *             database?: scalar|Param|null, // MongoDB database name // Default: null
 *             doctrine_connection?: scalar|Param|null, // Doctrine MongoDB connection name (mutually exclusive with mongodb_uri)
 *             mongodb_uri?: scalar|Param|null, // MongoDB connection URI (mutually exclusive with doctrine_connection)
 *             mongodb_uri_options?: list<mixed>,
 *             mongodb_driver_options?: list<mixed>,
 *         },
 *         lazy?: array{ // Lazy adapter for runtime storage selection
 *             source?: scalar|Param|null, // The service name of the storage to use at runtime
 *         },
 *         local?: array{
 *             directory?: scalar|Param|null, // Directory path for local storage
 *             lock?: int|Param, // Lock flags for file operations // Default: 0
 *             skip_links?: bool|Param, // Whether to skip symbolic links // Default: false
 *             lazy_root_creation?: bool|Param, // Whether to create the root directory lazily // Default: false
 *             permissions?: array{ // Unix permissions configuration for files and directories
 *                 file?: array{ // File permissions
 *                     public?: int|Param, // Public file permissions // Default: 420
 *                     private?: int|Param, // Private file permissions // Default: 384
 *                 },
 *                 dir?: array{ // Directory permissions
 *                     public?: int|Param, // Public directory permissions // Default: 493
 *                     private?: int|Param, // Private directory permissions // Default: 448
 *                 },
 *             },
 *         },
 *         memory?: array<mixed>,
 *         sftp?: array{
 *             host?: scalar|Param|null, // SFTP host
 *             username?: scalar|Param|null, // SFTP username
 *             password?: scalar|Param|null, // SFTP password (optional if using private key) // Default: null
 *             privateKey?: scalar|Param|null, // Path to private key file or private key content // Default: null
 *             passphrase?: scalar|Param|null, // Private key passphrase // Default: null
 *             port?: int|Param, // SFTP port number // Default: 22
 *             timeout?: int|Param, // Connection timeout in seconds // Default: 90
 *             hostFingerprint?: scalar|Param|null, // Host fingerprint for verification // Default: null
 *             connectivityChecker?: scalar|Param|null, // Connectivity checker service name // Default: null
 *             preferredAlgorithms?: list<mixed>,
 *             root?: scalar|Param|null, // SFTP root directory // Default: ""
 *             permissions?: array{ // Unix permissions configuration for files and directories
 *                 file?: array{ // File permissions
 *                     public?: int|Param, // Public file permissions // Default: 420
 *                     private?: int|Param, // Private file permissions // Default: 384
 *                 },
 *                 dir?: array{ // Directory permissions
 *                     public?: int|Param, // Public directory permissions // Default: 493
 *                     private?: int|Param, // Private directory permissions // Default: 448
 *                 },
 *             },
 *         },
 *         webdav?: array{
 *             client?: scalar|Param|null, // The WebDAV client service name
 *             prefix?: scalar|Param|null, // Optional path prefix to prepend to all paths // Default: ""
 *             visibility_handling?: "throw"|"ignore"|Param, // How to handle visibility operations // Default: "throw"
 *             manual_copy?: bool|Param, // Whether to handle copy operations manually // Default: false
 *             manual_move?: bool|Param, // Whether to handle move operations manually // Default: false
 *         },
 *         bunnycdn?: array{
 *             client?: scalar|Param|null, // The BunnyCDN client service name
 *             pull_zone?: scalar|Param|null, // The BunnyCDN pull zone name // Default: ""
 *         },
 *         service?: scalar|Param|null, // Reference to a custom adapter service (alternative to registered adapter types)
 *         visibility?: scalar|Param|null, // Default visibility for files // Default: null
 *         directory_visibility?: scalar|Param|null, // Default visibility for directories // Default: null
 *         retain_visibility?: scalar|Param|null, // Keeps the original file visibility (public/private) when copying or moving. // Default: null
 *         case_sensitive?: bool|Param, // Deprecated: The "case_sensitive" option is deprecated and will be removed in 4.0. // Default: true
 *         disable_asserts?: bool|Param, // Deprecated: The "disable_asserts" option is deprecated and will be removed in 4.0. // Default: false
 *         public_url?: list<scalar|Param|null>,
 *         path_normalizer?: scalar|Param|null, // Path normalizer service name (should implement League\Flysystem\PathNormalizer) // Default: null
 *         public_url_generator?: scalar|Param|null, // For adapter that do not provide public URLs or override adapter capabilities and public_url option, a public URL generator service name can be configured in the main Filesystem configuration (should implement League\Flysystem\PublicUrlGenerator) // Default: null
 *         temporary_url_generator?: scalar|Param|null, // For adapter that do not provide public URLs or override adapter capabilities, a temporary URL generator service name can be configured in the main Filesystem configuration (should implement League\Flysystem\TemporaryUrlGenerator) // Default: null
 *         read_only?: bool|Param, // Converts a file system to read-only // Default: false
 *     }>,
 * }
 * @psalm-type LiipImagineConfig = array{
 *     resolvers?: array<string, array{ // Default: []
 *         web_path?: array{
 *             web_root?: scalar|Param|null, // Default: "%kernel.project_dir%/public"
 *             cache_prefix?: scalar|Param|null, // Default: "media/cache"
 *         },
 *         aws_s3?: array{
 *             bucket?: scalar|Param|null,
 *             cache?: scalar|Param|null, // Default: false
 *             use_psr_cache?: bool|Param, // Default: false
 *             acl?: scalar|Param|null, // Default: "public-read"
 *             cache_prefix?: scalar|Param|null, // Default: ""
 *             client_id?: scalar|Param|null, // Default: null
 *             client_config?: list<mixed>,
 *             get_options?: array<string, scalar|Param|null>,
 *             put_options?: array<string, scalar|Param|null>,
 *             proxies?: array<string, scalar|Param|null>,
 *             ...<string, mixed>
 *         },
 *         flysystem?: array{
 *             filesystem_service?: scalar|Param|null,
 *             cache_prefix?: scalar|Param|null, // Default: ""
 *             root_url?: scalar|Param|null,
 *             visibility?: "public"|"private"|"noPredefinedVisibility"|Param, // Default: "public"
 *         },
 *     }>,
 *     loaders?: array<string, array{ // Default: []
 *         stream?: array{
 *             wrapper?: scalar|Param|null,
 *             context?: scalar|Param|null, // Default: null
 *         },
 *         filesystem?: array{
 *             locator?: "filesystem"|"filesystem_insecure"|Param, // Using the "filesystem_insecure" locator is not recommended due to a less secure resolver mechanism, but is provided for those using heavily symlinked projects. // Default: "filesystem"
 *             data_root?: Param|string|list<scalar|Param|null>,
 *             allow_unresolvable_data_roots?: bool|Param, // Default: false
 *             bundle_resources?: array{
 *                 enabled?: bool|Param, // Default: false
 *                 access_control_type?: "blacklist"|"whitelist"|Param, // Sets the access control method applied to bundle names in "access_control_list" into a blacklist or whitelist. // Default: "blacklist"
 *                 access_control_list?: list<scalar|Param|null>,
 *             },
 *         },
 *         flysystem?: array{
 *             filesystem_service?: scalar|Param|null,
 *         },
 *         asset_mapper?: array<mixed>,
 *         chain?: array{
 *             loaders?: list<scalar|Param|null>,
 *         },
 *     }>,
 *     driver?: scalar|Param|null, // Default: "gd"
 *     cache?: scalar|Param|null, // Default: "default"
 *     cache_base_path?: scalar|Param|null, // Default: ""
 *     data_loader?: scalar|Param|null, // Default: "default"
 *     default_image?: scalar|Param|null, // Default: null
 *     default_filter_set_settings?: array{
 *         quality?: scalar|Param|null, // Default: 100
 *         jpeg_quality?: scalar|Param|null, // Default: null
 *         png_compression_level?: scalar|Param|null, // Default: null
 *         png_compression_filter?: scalar|Param|null, // Default: null
 *         format?: scalar|Param|null, // Default: null
 *         animated?: bool|Param, // Default: false
 *         cache?: scalar|Param|null, // Default: null
 *         data_loader?: scalar|Param|null, // Default: null
 *         default_image?: scalar|Param|null, // Default: null
 *         filters?: array<string, array<string, mixed>>,
 *         post_processors?: array<string, array<string, mixed>>,
 *     },
 *     controller?: array{
 *         filter_action?: scalar|Param|null, // Default: "Liip\\ImagineBundle\\Controller\\ImagineController::filterAction"
 *         filter_runtime_action?: scalar|Param|null, // Default: "Liip\\ImagineBundle\\Controller\\ImagineController::filterRuntimeAction"
 *         redirect_response_code?: int|Param, // Default: 302
 *     },
 *     filter_sets?: array<string, array{ // Default: []
 *         quality?: scalar|Param|null,
 *         jpeg_quality?: scalar|Param|null,
 *         png_compression_level?: scalar|Param|null,
 *         png_compression_filter?: scalar|Param|null,
 *         format?: scalar|Param|null,
 *         animated?: bool|Param,
 *         cache?: scalar|Param|null,
 *         data_loader?: scalar|Param|null,
 *         default_image?: scalar|Param|null,
 *         filters?: array<string, array<string, mixed>>,
 *         post_processors?: array<string, array<string, mixed>>,
 *     }>,
 *     twig?: array{
 *         mode?: "none"|"lazy"|"legacy"|Param, // Twig mode: none/lazy/legacy (default) // Default: "legacy"
 *         assets_version?: scalar|Param|null, // Default: null
 *     },
 *     enqueue?: bool|Param, // Enables integration with enqueue if set true. Allows resolve image caches in background by sending messages to MQ. // Default: false
 *     messenger?: bool|array{ // Enables integration with symfony/messenger if set true. Warmup image caches in background by sending messages to MQ.
 *         enabled?: bool|Param, // Default: false
 *     },
 *     templating?: bool|Param, // Enables integration with symfony/templating component // Default: true
 *     webp?: array{
 *         generate?: bool|Param, // Default: false
 *         quality?: int|Param, // Default: 100
 *         cache?: scalar|Param|null, // Default: null
 *         data_loader?: scalar|Param|null, // Default: null
 *         post_processors?: array<string, array<string, mixed>>,
 *     },
 *     ...<string, mixed>
 * }
 * @psalm-type UxIconsConfig = array{
 *     icon_dir?: scalar|Param|null, // The local directory where icons are stored. // Default: "%kernel.project_dir%/assets/icons"
 *     default_icon_attributes?: array<string, scalar|Param|null>,
 *     icon_sets?: array<string, array{ // the icon set prefix (e.g. "acme") // Default: []
 *         path?: scalar|Param|null, // The local icon set directory path. (cannot be used with 'alias')
 *         alias?: scalar|Param|null, // The remote icon set identifier. (cannot be used with 'path')
 *         icon_attributes?: array<string, scalar|Param|null>,
 *         suffixes?: array<string, array{ // The suffix name (e.g. "solid", "20-solid") // Default: []
 *             icon_attributes?: array<string, scalar|Param|null>,
 *         }>,
 *     }>,
 *     aliases?: array<string, string|Param>,
 *     iconify?: bool|array{ // Configuration for the remote icon service.
 *         enabled?: bool|Param, // Default: true
 *         on_demand?: bool|Param, // Whether to download icons "on demand". // Default: true
 *         auto_lock?: bool|Param, // Persist "on demand" icons to the local icon directory (see "icon_dir"). Recommended in dev only. Requires "on_demand" to be enabled. // Default: false
 *         endpoint?: scalar|Param|null, // The endpoint for the Iconify icons API. // Default: "https://api.iconify.design"
 *     },
 *     ignore_not_found?: bool|Param, // Ignore error when an icon is not found. Set to 'true' to fail silently. // Default: false
 * }
 * @psalm-type DoctrineDiagramConfig = array{
 *     er?: array{
 *         filename?: scalar|Param|null, // Default: "%kernel.project_dir%/er"
 *         size?: "mini"|"midi"|"maxi"|Param, // Default: "midi"
 *         theme?: scalar|Param|null, // Default: "_none_"
 *         connection?: scalar|Param|null, // Default: null
 *         exclude?: list<scalar|Param|null>,
 *     },
 *     class?: array{
 *         filename?: scalar|Param|null, // Default: "%kernel.project_dir%/class"
 *         size?: "mini"|"midi"|"maxi"|Param, // Default: "midi"
 *         theme?: scalar|Param|null, // Default: "_none_"
 *         em?: scalar|Param|null, // Default: null
 *         exclude?: list<scalar|Param|null>,
 *     },
 *     convert?: array{
 *         format?: "puml"|"png"|"svg"|Param, // Default: "svg"
 *         converter?: "auto"|"jar"|"server"|Param, // Default: "auto"
 *         jar?: scalar|Param|null, // Default: null
 *         server?: scalar|Param|null, // Default: "http://www.plantuml.com/plantuml"
 *     },
 * }
 * @psalm-type InspectorConfig = array{
 *     enabled?: bool|Param, // Default: true
 *     url?: scalar|Param|null, // Default: "https://ingest.inspector.dev"
 *     ingestion_key?: scalar|Param|null, // Default: null
 *     unhandled_exceptions?: bool|Param, // Default: true
 *     messenger?: bool|Param, // Default: true
 *     query?: bool|Param, // Default: true
 *     query_bindings?: bool|Param, // Default: true
 *     http_client?: bool|Param, // Default: true
 *     templates?: bool|Param, // Default: true
 *     user?: bool|Param, // Default: true
 *     transport?: scalar|Param|null, // Default: "async"
 *     ignore_routes?: list<scalar|Param|null>,
 *     ignore_commands?: list<scalar|Param|null>,
 *     ignore_messages?: list<scalar|Param|null>,
 * }
 * @psalm-type SurvosCodeConfig = array{
 *     base_layout?: scalar|Param|null, // Default: "base.html.twig"
 * }
 * @psalm-type SurvosMeiliConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: "/meili"
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     core_name?: scalar|Param|null, // Default: "core"
 *     enabled?: bool|Param, // Default: true
 *     meiliUiUrl?: scalar|Param|null, // Base URL of the Meilisearch UI (riccox). Used to generate per-index links. Override via MEILI_UI_URL env var. // Default: "http://127.0.0.1:24900/ins/0"
 *     host?: scalar|Param|null, // Default: "%env(default::MEILI_SERVER)%"
 *     apiKey?: scalar|Param|null, // Default: "%env(default::MEILI_ADMIN_KEY)%"
 *     transport?: scalar|Param|null, // Default: "%env(default::MEILI_TRANSPORT)%"
 *     searchKey?: scalar|Param|null, // Default: "%env(default::MEILI_SEARCH_KEY)%"
 *     meiliPrefix?: scalar|Param|null, // Default: "%env(default::MEILI_PREFIX)%"
 *     translationStyle?: scalar|Param|null, // Default: "simple"
 *     passLocale?: bool|Param, // Default: false
 *     multiLingual?: bool|Param, // turn on multi-lingual indexing // Default: false
 *     maxValuesPerFacet?: int|Param, // Default: 1000
 *     tools?: list<array{ // Default: []
 *         label?: scalar|Param|null,
 *         url?: scalar|Param|null,
 *     }>,
 *     embedders?: array<string, array{ // Default: []
 *         source?: scalar|Param|null,
 *         model?: scalar|Param|null,
 *         apiKey?: scalar|Param|null, // Default: null
 *         for?: scalar|Param|null, // Default: null
 *         template?: scalar|Param|null, // Default: null
 *         documentTemplateMaxBytes?: int|Param, // Default: 4096
 *         maxTokensPerDoc?: int|Param, // Default: null
 *         examples?: list<scalar|Param|null>,
 *     }>,
 *     pricing?: array{
 *         embedders?: array<string, scalar|Param|null>,
 *     },
 *     meili_settings?: array{
 *         typoTolerance?: array{
 *             enabled?: bool|Param, // Default: true
 *             oneTypo?: int|Param, // Default: 5
 *             twoTypos?: int|Param, // Default: 9
 *             disableOnWords?: list<scalar|Param|null>,
 *             disableOnAttributes?: list<scalar|Param|null>,
 *             disableOnNumbers?: bool|Param, // Default: false
 *         },
 *         faceting?: array{
 *             maxValuesPerFacet?: int|Param, // Default: 1000
 *             sortFacetValuesBy?: array<string, scalar|Param|null>,
 *         },
 *         pagination?: array{
 *             maxTotalHits?: int|Param, // Default: 1000
 *         },
 *         facetSearch?: bool|Param, // Default: true
 *         prefixSearch?: scalar|Param|null, // Default: "indexingTime"
 *     },
 *     entity_dirs?: list<scalar|Param|null>,
 *     file_proxy?: array{
 *         enabled?: bool|Param, // Default: true
 *         allow_hidden?: bool|Param, // Default: false
 *         cache_control?: scalar|Param|null, // Default: "private, max-age=60"
 *         roots?: list<scalar|Param|null>,
 *     },
 *     chat?: array{
 *         workspaces?: array<string, array{ // Default: []
 *             source?: scalar|Param|null, // LLM provider: openAi | azureOpenAi | mistral | gemini | vLlm // Default: "openAi"
 *             apiKey?: scalar|Param|null, // Provider API key (use %env(OPENAI_API_KEY)%) // Default: null
 *             model?: scalar|Param|null, // Model sent in each completion request (not stored in workspace settings) // Default: "gpt-4o-mini"
 *             baseUrl?: scalar|Param|null, // Default: null
 *             orgId?: scalar|Param|null, // Default: null
 *             projectId?: scalar|Param|null, // Default: null
 *             apiVersion?: scalar|Param|null, // Default: null
 *             deploymentId?: scalar|Param|null, // Default: null
 *             label?: scalar|Param|null, // Human-readable label used in dynamic prompts (defaults to indexName) // Default: null
 *             curatorName?: scalar|Param|null, // Optional explicit curator display name for this workspace template // Default: null
 *             curatorNameByIndex?: list<scalar|Param|null>,
 *             detailUrlPattern?: scalar|Param|null, // URL pattern for item detail pages; use {id} as placeholder e.g. /product/{id} // Default: null
 *             schemaUrl?: scalar|Param|null, // Optional OpenAPI schema URL used to explain field meanings in collection overview responses // Default: null
 *             examples?: list<scalar|Param|null>,
 *             examplesByIndex?: list<list<scalar|Param|null>>,
 *             prompts?: array{ // Static prompt overrides — these win over dynamic template rendering
 *                 system?: scalar|Param|null, // Default: null
 *                 searchFilterParam?: scalar|Param|null, // Default: null
 *                 searchDescription?: scalar|Param|null, // Default: null
 *                 searchQParam?: scalar|Param|null, // Default: null
 *                 searchIndexUidParam?: scalar|Param|null, // Pin the index UID — prevents Meilisearch generating a full enum of all indexes, which blows the OpenAI context limit. // Default: null
 *             },
 *             indexes?: list<scalar|Param|null>,
 *         }>,
 *     },
 * }
 * @psalm-type SurvosStateConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: "/state"
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     queue_prefix?: scalar|Param|null, // Default: ""
 *     base_layout?: scalar|Param|null, // Default: "base.html.twig"
 *     enable_dynamic_routing?: bool|Param, // Default: true
 *     workflow_paths?: list<scalar|Param|null>,
 *     async_transport_dsn?: scalar|Param|null, // Default: "doctrine://default"
 *     queue_driver?: "doctrine"|"rabbitmq"|Param, // Default: "doctrine"
 * }
 * @psalm-type ZenstruckMessengerMonitorConfig = array{
 *     storage?: array{
 *         exclude?: list<scalar|Param|null>,
 *         orm?: array{
 *             entity_class?: scalar|Param|null, // Your Doctrine entity class that extends "Zenstruck\Messenger\Monitor\History\Model\ProcessedMessage"
 *         },
 *     },
 *     cache?: array{
 *         pool?: scalar|Param|null, // Cache pool to use for worker cache. // Default: "cache.app"
 *         expired_worker_ttl?: int|Param, // How long to keep expired workers in cache (in seconds). // Default: 3600
 *     },
 * }
 * @psalm-type SurvosEzConfig = array{
 *     enabled?: bool|Param, // Default: true
 * }
 * @psalm-type SurvosDeploymentConfig = array{
 *     enabled?: bool|Param, // Default: true
 * }
 * @psalm-type SurvosImportConfig = array{
 *     dir?: scalar|Param|null, // Default directory for data files // Default: "data"
 *     dto_namespace_roots?: list<scalar|Param|null>,
 *     dto_mappings?: array<string, scalar|Param|null>,
 * }
 * @psalm-type SurvosApiGridConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: ""
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     stimulus_controller?: scalar|Param|null, // The stimulus controller to use, should extend @survos/api-grid/api-grid // Default: "@survos/api-grid/api-grid"
 *     meiliHost?: scalar|Param|null, // Default: "%env(MEILI_SERVER)%"
 *     meiliKey?: scalar|Param|null, // Default: "%env(MEILI_API_KEY)%"
 *     meiliPrefix?: scalar|Param|null, // Default: "%env(MEILI_PREFIX)%"
 *     meili_provider?: bool|Param, // Register MeiliSearchStateProvider as a global api_platform.state_provider. Only enable when Meili is configured and entities should be served from it. // Default: false
 *     passLocale?: bool|Param, // Default: false
 *     maxValuesPerFacet?: int|Param, // https://www.meilisearch.com/docs/reference/api/settings#faceting-object // Default: 1000
 * }
 * @psalm-type SurvosJsTwigConfig = array{
 *     debug?: bool|Param, // Default: false
 *     version?: scalar|Param|null, // Default: 1
 *     db?: scalar|Param|null, // Default: "db"
 *     routing?: array{
 *         routes_to_expose?: list<scalar|Param|null>,
 *     },
 *     stores?: list<array{ // Default: []
 *         batch?: int|Param, // batch size when loading api // Default: null
 *         name?: scalar|Param|null, // the store name
 *         schema?: scalar|Param|null, // the index definition
 *         url?: scalar|Param|null, // the API to use to load if empty. json-ld iterates through pages
 *         response_key?: scalar|Param|null, // key if API returns an object response, e.g. dummyjson returns {'products': [...]}
 *     }>,
 * }
 * @psalm-type ApiPlatformConfig = array{
 *     title?: scalar|Param|null, // The title of the API. // Default: ""
 *     description?: scalar|Param|null, // The description of the API. // Default: ""
 *     version?: scalar|Param|null, // The version of the API. // Default: "0.0.0"
 *     show_webby?: bool|Param, // If true, show Webby on the documentation page // Default: true
 *     use_symfony_listeners?: bool|Param, // Uses Symfony event listeners instead of the ApiPlatform\Symfony\Controller\MainController. // Default: false
 *     name_converter?: scalar|Param|null, // Specify a name converter to use. // Default: null
 *     asset_package?: scalar|Param|null, // Specify an asset package name to use. // Default: null
 *     path_segment_name_generator?: scalar|Param|null, // Specify a path name generator to use. // Default: "api_platform.metadata.path_segment_name_generator.underscore"
 *     inflector?: scalar|Param|null, // Specify an inflector to use. // Default: "api_platform.metadata.inflector"
 *     validator?: array{
 *         serialize_payload_fields?: mixed, // Set to null to serialize all payload fields when a validation error is thrown, or set the fields you want to include explicitly. // Default: []
 *     },
 *     jsonapi?: array{
 *         use_iri_as_id?: bool|Param|null, // Set to false to use entity identifiers instead of IRIs as the "id" field in JSON:API responses. Defaults to true; this default will change to false in API Platform 5.0. // Default: null
 *         allow_client_generated_id?: bool|Param, // Allow client-generated IDs on JSON:API POST per https://jsonapi.org/format/#crud-creating-client-ids. Off by default to prevent id spoofing on public endpoints. // Default: false
 *     },
 *     eager_loading?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         fetch_partial?: bool|Param, // Fetch only partial data according to serialization groups. If enabled, Doctrine ORM entities will not work as expected if any of the other fields are used. // Default: false
 *         max_joins?: int|Param, // Max number of joined relations before EagerLoading throws a RuntimeException // Default: 30
 *         force_eager?: bool|Param, // Force join on every relation. If disabled, it will only join relations having the EAGER fetch mode. // Default: true
 *     },
 *     handle_symfony_errors?: bool|Param, // Allows to handle symfony exceptions. // Default: false
 *     enable_swagger?: bool|Param, // Enable the Swagger documentation and export. // Default: true
 *     enable_json_streamer?: bool|Param, // Enable json streamer. // Default: false
 *     enable_swagger_ui?: bool|Param, // Enable Swagger UI // Default: true
 *     enable_re_doc?: bool|Param, // Enable ReDoc // Default: true
 *     enable_scalar?: bool|Param, // Enable Scalar API Reference // Default: true
 *     enable_entrypoint?: bool|Param, // Enable the entrypoint // Default: true
 *     enable_docs?: bool|Param, // Enable the docs // Default: true
 *     enable_head_request_optimization?: bool|Param, // Skip response body construction on HEAD requests so collections are not iterated. Disable to process HEAD identically to GET. // Default: true
 *     enable_profiler?: bool|Param, // Enable the data collector and the WebProfilerBundle integration. // Default: true
 *     enable_phpdoc_parser?: bool|Param, // Enable resource metadata collector using PHPStan PhpDocParser. // Default: true
 *     collection?: array{
 *         exists_parameter_name?: scalar|Param|null, // The name of the query parameter to filter on nullable field values. // Default: "exists"
 *         order?: scalar|Param|null, // The default order of results. // Default: "ASC"
 *         order_parameter_name?: scalar|Param|null, // The name of the query parameter to order results. // Default: "order"
 *         order_nulls_comparison?: "nulls_smallest"|"nulls_largest"|"nulls_always_first"|"nulls_always_last"|Param|null, // The nulls comparison strategy. // Default: null
 *         pagination?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *             page_parameter_name?: scalar|Param|null, // The default name of the parameter handling the page number. // Default: "page"
 *             enabled_parameter_name?: scalar|Param|null, // The name of the query parameter to enable or disable pagination. // Default: "pagination"
 *             items_per_page_parameter_name?: scalar|Param|null, // The name of the query parameter to set the number of items per page. // Default: "itemsPerPage"
 *             partial_parameter_name?: scalar|Param|null, // The name of the query parameter to enable or disable partial pagination. // Default: "partial"
 *         },
 *     },
 *     mapping?: array{
 *         imports?: list<scalar|Param|null>,
 *         paths?: list<scalar|Param|null>,
 *     },
 *     serializer?: array{
 *         hydra_prefix?: bool|Param, // Use the "hydra:" prefix. // Default: false
 *     },
 *     doctrine?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     doctrine_mongodb_odm?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *     },
 *     oauth?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         clientId?: scalar|Param|null, // The oauth client id. // Default: ""
 *         clientSecret?: scalar|Param|null, // The OAuth client secret. Never use this parameter in your production environment. It exposes crucial security information. This feature is intended for dev/test environments only. Enable "oauth.pkce" instead // Default: ""
 *         pkce?: bool|Param, // Enable the oauth PKCE. // Default: false
 *         type?: scalar|Param|null, // The oauth type. // Default: "oauth2"
 *         flow?: scalar|Param|null, // The oauth flow grant type. // Default: "application"
 *         tokenUrl?: scalar|Param|null, // The oauth token url. // Default: ""
 *         authorizationUrl?: scalar|Param|null, // The oauth authentication url. // Default: ""
 *         refreshUrl?: scalar|Param|null, // The oauth refresh url. // Default: ""
 *         scopes?: list<scalar|Param|null>,
 *     },
 *     graphql?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         default_ide?: scalar|Param|null, // Default: "graphiql"
 *         graphiql?: bool|array{
 *             enabled?: bool|Param, // Default: false
 *         },
 *         introspection?: bool|array{
 *             enabled?: bool|Param, // Default: true
 *         },
 *         max_query_depth?: int|Param, // Default: 20
 *         max_query_complexity?: int|Param, // Default: 500
 *         nesting_separator?: scalar|Param|null, // The separator to use to filter nested fields. // Default: "_"
 *         collection?: array{
 *             pagination?: bool|array{
 *                 enabled?: bool|Param, // Default: true
 *             },
 *         },
 *     },
 *     swagger?: array{
 *         persist_authorization?: bool|Param, // Persist the SwaggerUI Authorization in the localStorage. // Default: false
 *         with_credentials?: bool|Param, // Send credentials (cookies, authorization headers) on Swagger UI cross-origin requests (e.g. when running behind Cloudflare Access). // Default: false
 *         versions?: list<scalar|Param|null>,
 *         api_keys?: array<string, array{ // Default: []
 *             name?: scalar|Param|null, // The name of the header or query parameter containing the api key.
 *             type?: "query"|"header"|Param, // Whether the api key should be a query parameter or a header.
 *         }>,
 *         http_auth?: array<string, array{ // Default: []
 *             scheme?: scalar|Param|null, // The OpenAPI HTTP auth scheme, for example "bearer"
 *             bearerFormat?: scalar|Param|null, // The OpenAPI HTTP bearer format
 *         }>,
 *         swagger_ui_extra_configuration?: mixed, // To pass extra configuration to Swagger UI, like docExpansion or filter. // Default: []
 *     },
 *     http_cache?: array{
 *         public?: bool|Param|null, // To make all responses public by default. // Default: null
 *         invalidation?: bool|array{ // Enable the tags-based cache invalidation system.
 *             enabled?: bool|Param, // Default: false
 *             urls?: list<scalar|Param|null>,
 *             scoped_clients?: list<scalar|Param|null>,
 *             max_header_length?: int|Param, // Max header length supported by the cache server. // Default: 7500
 *             request_options?: mixed, // To pass options to the client charged with the request. // Default: []
 *             purger?: scalar|Param|null, // Specify a purger to use (available values: "api_platform.http_cache.purger.varnish.ban", "api_platform.http_cache.purger.varnish.xkey", "api_platform.http_cache.purger.souin"). // Default: "api_platform.http_cache.purger.varnish"
 *         },
 *     },
 *     mercure?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         hub_url?: scalar|Param|null, // The URL sent in the Link HTTP header. If not set, will default to the URL for MercureBundle's default hub. // Default: null
 *         include_type?: bool|Param, // Always include @type in updates (including delete ones). // Default: false
 *     },
 *     messenger?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *     },
 *     elasticsearch?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         hosts?: list<scalar|Param|null>,
 *         ssl_ca_bundle?: scalar|Param|null, // Path to the SSL CA bundle file for Elasticsearch SSL verification. // Default: null
 *         ssl_verification?: bool|Param, // Enable or disable SSL verification for Elasticsearch connections. // Default: true
 *         client?: "elasticsearch"|"opensearch"|Param, // The search engine client to use: "elasticsearch" or "opensearch". // Default: "elasticsearch"
 *     },
 *     meilisearch?: bool|array{
 *         enabled?: bool|Param, // Default: false
 *         url?: scalar|Param|null, // The Meilisearch instance URL. // Default: "http://localhost:7700"
 *         api_key?: scalar|Param|null, // The Meilisearch API key. // Default: null
 *     },
 *     openapi?: array{
 *         contact?: array{
 *             name?: scalar|Param|null, // The identifying name of the contact person/organization. // Default: null
 *             url?: scalar|Param|null, // The URL pointing to the contact information. MUST be in the format of a URL. // Default: null
 *             email?: scalar|Param|null, // The email address of the contact person/organization. MUST be in the format of an email address. // Default: null
 *         },
 *         termsOfService?: scalar|Param|null, // A URL to the Terms of Service for the API. MUST be in the format of a URL. // Default: null
 *         tags?: list<array{ // Default: []
 *             name?: scalar|Param|null,
 *             description?: scalar|Param|null, // Default: null
 *         }>,
 *         license?: array{
 *             name?: scalar|Param|null, // The license name used for the API. // Default: null
 *             url?: scalar|Param|null, // URL to the license used for the API. MUST be in the format of a URL. // Default: null
 *             identifier?: scalar|Param|null, // An SPDX license expression for the API. The identifier field is mutually exclusive of the url field. // Default: null
 *         },
 *         swagger_ui_extra_configuration?: mixed, // To pass extra configuration to Swagger UI, like docExpansion or filter. // Default: []
 *         scalar_extra_configuration?: mixed, // To pass extra configuration to Scalar API Reference, like theme or darkMode. // Default: []
 *         overrideResponses?: bool|Param, // Whether API Platform adds automatic responses to the OpenAPI documentation. // Default: true
 *         error_resource_class?: scalar|Param|null, // The class used to represent errors in the OpenAPI documentation. // Default: null
 *         validation_error_resource_class?: scalar|Param|null, // The class used to represent validation errors in the OpenAPI documentation. // Default: null
 *     },
 *     maker?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         namespace_prefix?: scalar|Param|null, // Add a prefix to all maker generated classes. e.g set it to "Api" to set the maker namespace to "App\Api\" (if the maker.root_namespace config is App). e.g. App\Api\State\MyStateProcessor // Default: ""
 *     },
 *     mcp?: bool|array{
 *         enabled?: bool|Param, // Default: true
 *         format?: scalar|Param|null, // The serialization format used for MCP tool input/output. Must be a format registered in api_platform.formats (e.g. "jsonld", "json", "jsonapi"). // Default: "jsonld"
 *     },
 *     exception_to_status?: array<string, int|Param>,
 *     formats?: array<string, array{ // Default: {"jsonld":{"mime_types":["application/ld+json"]}}
 *         mime_types?: list<scalar|Param|null>,
 *     }>,
 *     patch_formats?: array<string, array{ // Default: {"json":{"mime_types":["application/merge-patch+json"]}}
 *         mime_types?: list<scalar|Param|null>,
 *     }>,
 *     docs_formats?: array<string, array{ // Default: {"jsonld":{"mime_types":["application/ld+json"]},"jsonopenapi":{"mime_types":["application/vnd.openapi+json"]},"html":{"mime_types":["text/html"]},"yamlopenapi":{"mime_types":["application/vnd.openapi+yaml"]}}
 *         mime_types?: list<scalar|Param|null>,
 *     }>,
 *     error_formats?: array<string, array{ // Default: {"jsonld":{"mime_types":["application/ld+json"]},"jsonproblem":{"mime_types":["application/problem+json"]},"json":{"mime_types":["application/problem+json","application/json"]}}
 *         mime_types?: list<scalar|Param|null>,
 *     }>,
 *     jsonschema_formats?: list<scalar|Param|null>,
 *     defaults?: array{
 *         uri_template?: mixed,
 *         short_name?: mixed,
 *         description?: mixed,
 *         types?: mixed,
 *         operations?: mixed,
 *         formats?: mixed,
 *         input_formats?: mixed,
 *         output_formats?: mixed,
 *         uri_variables?: mixed,
 *         route_prefix?: mixed,
 *         defaults?: mixed,
 *         requirements?: mixed,
 *         options?: mixed,
 *         stateless?: mixed,
 *         sunset?: mixed,
 *         accept_patch?: mixed,
 *         status?: mixed,
 *         host?: mixed,
 *         schemes?: mixed,
 *         condition?: mixed,
 *         controller?: mixed,
 *         class?: mixed,
 *         url_generation_strategy?: mixed,
 *         deprecation_reason?: mixed,
 *         headers?: mixed,
 *         cache_headers?: mixed,
 *         normalization_context?: mixed,
 *         denormalization_context?: mixed,
 *         collect_denormalization_errors?: mixed,
 *         hydra_context?: mixed,
 *         jsonld_context?: mixed,
 *         openapi?: mixed,
 *         validation_context?: mixed,
 *         filters?: mixed,
 *         mercure?: mixed,
 *         messenger?: mixed,
 *         input?: mixed,
 *         output?: mixed,
 *         order?: mixed,
 *         fetch_partial?: mixed,
 *         force_eager?: mixed,
 *         pagination_client_enabled?: mixed,
 *         pagination_client_items_per_page?: mixed,
 *         pagination_client_partial?: mixed,
 *         pagination_via_cursor?: mixed,
 *         pagination_enabled?: mixed,
 *         pagination_fetch_join_collection?: mixed,
 *         pagination_use_output_walkers?: mixed,
 *         pagination_items_per_page?: mixed,
 *         pagination_maximum_items_per_page?: mixed,
 *         pagination_partial?: mixed,
 *         pagination_type?: mixed,
 *         security?: mixed,
 *         security_message?: mixed,
 *         security_post_denormalize?: mixed,
 *         security_post_denormalize_message?: mixed,
 *         security_post_validation?: mixed,
 *         security_post_validation_message?: mixed,
 *         composite_identifier?: mixed,
 *         exception_to_status?: mixed,
 *         query_parameter_validation_enabled?: mixed,
 *         links?: mixed,
 *         graph_ql_operations?: mixed,
 *         provider?: mixed,
 *         processor?: mixed,
 *         state_options?: mixed,
 *         rules?: mixed,
 *         policy?: mixed,
 *         middleware?: mixed,
 *         parameters?: array<string, array{ // Default: []
 *             key?: mixed,
 *             schema?: mixed,
 *             open_api?: mixed,
 *             provider?: mixed,
 *             filter?: mixed,
 *             property?: mixed,
 *             description?: mixed,
 *             properties?: mixed,
 *             required?: mixed,
 *             priority?: mixed,
 *             hydra?: mixed,
 *             constraints?: mixed,
 *             security?: mixed,
 *             security_message?: mixed,
 *             extra_properties?: mixed,
 *             filter_context?: mixed,
 *             native_type?: mixed,
 *             cast_to_array?: mixed,
 *             cast_to_native_type?: mixed,
 *             cast_fn?: mixed,
 *             default?: mixed,
 *             filter_class?: mixed,
 *             ...<string, mixed>
 *         }>,
 *         strict_query_parameter_validation?: mixed,
 *         hide_hydra_operation?: mixed,
 *         json_stream?: mixed,
 *         throw_on_not_found?: mixed,
 *         extra_properties?: mixed,
 *         map?: mixed,
 *         mcp?: mixed,
 *         route_name?: mixed,
 *         errors?: mixed,
 *         read?: mixed,
 *         deserialize?: mixed,
 *         validate?: mixed,
 *         write?: mixed,
 *         serialize?: mixed,
 *         content_negotiation?: mixed,
 *         priority?: mixed,
 *         name?: mixed,
 *         allow_create?: mixed,
 *         item_uri_template?: mixed,
 *         ...<string, mixed>
 *     },
 *     ...<string, mixed>
 * }
 * @psalm-type AiConfig = array{
 *     platform?: array{
 *         albert?: array{
 *             api_key?: string|Param,
 *             base_url?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         amazeeai?: array{
 *             base_url?: string|Param,
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         anthropic?: array{
 *             api_key?: string|Param,
 *             version?: string|Param, // Default: null
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *             cache_retention?: "none"|"short"|"long"|Param, // Prompt cache retention policy for Anthropic models // Default: "short"
 *         },
 *         azure?: array<string, array{ // Default: []
 *             api_key?: string|Param,
 *             base_url?: string|Param,
 *             deployment?: string|Param,
 *             api_version?: string|Param, // The used API version
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         }>,
 *         bedrock?: array<string, array{ // Default: []
 *             bedrock_runtime_client?: string|Param, // Service ID of the Bedrock runtime client to use // Default: null
 *             model_catalog?: string|Param, // Default: null
 *         }>,
 *         cache?: array<string, array{ // Default: []
 *             platform?: string|Param,
 *             service?: string|Param, // The cache service id as defined under the "cache" configuration key // Default: "cache.app"
 *             cache_key?: string|Param, // Key used to store platform results, if not set, the current platform name will be used, the "prompt_cache_key" can be set during platform call to override this value
 *             ttl?: int|Param,
 *         }>,
 *         cartesia?: array{
 *             api_key?: string|Param,
 *             version?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         cerebras?: array{
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         cohere?: array{
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         decart?: array{
 *             api_key?: string|Param,
 *             host?: string|Param, // Default: "https://api.decart.ai/v1"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         deepgram?: array{
 *             api_key?: string|Param,
 *             endpoint?: string|Param, // Deepgram REST API endpoint // Default: "https://api.deepgram.com/v1/"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         deepseek?: array{
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         dockermodelrunner?: array{
 *             host_url?: string|Param, // Default: "http://127.0.0.1:12434"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         elevenlabs?: array{
 *             api_key?: string|Param,
 *             endpoint?: string|Param, // Default: "https://api.elevenlabs.io/v1/"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         failover?: array<string, array{ // Default: []
 *             platforms?: list<scalar|Param|null>,
 *             rate_limiter?: string|Param,
 *         }>,
 *         gemini?: array{
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         generic?: array<string, array{ // Default: []
 *             base_url?: string|Param,
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *             model_catalog?: string|Param, // Service ID of the model catalog to use
 *             supports_completions?: bool|Param, // Default: true
 *             supports_embeddings?: bool|Param, // Default: true
 *             completions_path?: string|Param, // Default: "/v1/chat/completions"
 *             embeddings_path?: string|Param, // Default: "/v1/embeddings"
 *         }>,
 *         huggingface?: array{
 *             api_key?: string|Param,
 *             provider?: string|Param, // Default: "hf-inference"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         lmstudio?: array{
 *             host_url?: string|Param, // Default: "http://127.0.0.1:1234"
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         minimax?: array{
 *             endpoint?: string|Param, // Default: "https://api.minimax.io/v1"
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         mistral?: array{
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         ollama?: array{
 *             endpoint?: string|Param, // Endpoint for Ollama (e.g. "http://127.0.0.1:11434" for local, or a cloud endpoint). If null, the http_client is used as-is and must already be configured with a base URI.
 *             api_key?: string|Param, // API key for Ollama Cloud authentication (optional for local usage)
 *             http_client?: string|Param, // Service ID of the HTTP client to use. When "endpoint" is null, this client must be pre-configured (e.g. with a base_uri). // Default: "http_client"
 *         },
 *         openai?: array{
 *             api_key?: string|Param,
 *             region?: scalar|Param|null, // The region for OpenAI API (EU, US, or null for default) // Default: null
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         openresponses?: array<string, array{ // Default: []
 *             base_url?: string|Param,
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *             model_catalog?: string|Param, // Service ID of the model catalog to use
 *             responses_path?: string|Param, // Default: "/v1/responses"
 *         }>,
 *         openrouter?: array{
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         ovh?: array{
 *             api_key?: scalar|Param|null,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         perplexity?: array{
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         scaleway?: array{
 *             api_key?: scalar|Param|null,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         transformersphp?: array<mixed>,
 *         vertexai?: array{
 *             location?: string|Param, // Required for the project-scoped endpoint. Must be set together with "project_id". // Default: null
 *             project_id?: string|Param, // Required for the project-scoped endpoint. Must be set together with "location". // Default: null
 *             api_key?: string|Param, // When set without "location" and "project_id", uses the global endpoint. Note: API keys only identify the project for billing and do not provide identity-based access control. For production use with IAM, audit logging, or data residency, prefer the project-scoped endpoint with service account authentication. // Default: null
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *         voyage?: array{
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *         },
 *     },
 *     model?: array<string, array<string, array{ // Default: []
 *         class?: string|Param, // The fully qualified class name of the model (must extend Symfony\AI\Platform\Model) // Default: "Symfony\\AI\\Platform\\Model"
 *         capabilities?: list<value-of<\Symfony\AI\Platform\Capability>|\Symfony\AI\Platform\Capability|Param>,
 *     }>>,
 *     agent?: array<string, array{ // Default: []
 *         platform?: string|Param, // Service name of platform // Default: "Symfony\\AI\\Platform\\PlatformInterface"
 *         model?: mixed,
 *         memory?: mixed, // Memory configuration: string for static memory, or array with "service" key for service reference // Default: null
 *         prompt?: Param|string|array{ // The system prompt configuration
 *             text?: string|Param, // The system prompt text
 *             file?: string|Param, // Path to file containing the system prompt
 *             include_tools?: bool|Param, // Include tool definitions at the end of the system prompt // Default: false
 *             enable_translation?: bool|Param, // Enable translation for the system prompt // Default: false
 *             translation_domain?: string|Param, // The translation domain for the system prompt // Default: null
 *         },
 *         tools?: bool|array{ // Tools are opt-in: set to true to inject all services tagged with "ai.tool", or configure an explicit list of tools. When the option is omitted (or set to null or false), no tools are registered.
 *             enabled?: bool|Param, // Default: false
 *             services?: list<Param|string|array{ // Default: []
 *                 service?: string|Param,
 *                 agent?: string|Param,
 *                 name?: string|Param,
 *                 description?: string|Param,
 *                 method?: string|Param,
 *             }>,
 *         },
 *         keep_tool_messages?: bool|Param, // Keep tool messages in the conversation history // Default: false
 *         include_sources?: bool|Param, // Include sources exposed by tools as part of the tool result metadata // Default: false
 *         max_tool_calls?: scalar|Param|null, // Maximum number of tool calls per agent call, null to disable // Default: 50
 *         fault_tolerant_toolbox?: bool|Param, // Continue the agent run even if a tool call fails // Default: true
 *         speech?: bool|array{ // Speech (TTS/STT) decorator configuration
 *             enabled?: bool|Param, // Default: true
 *             text_to_speech_platform?: string|Param, // Service name of the TTS platform (e.g. ai.platform.elevenlabs). // Default: null
 *             speech_to_text_platform?: string|Param, // Service name of the STT platform (e.g. ai.platform.openai). // Default: null
 *             tts_model?: string|Param, // Text-to-speech model name // Default: null
 *             tts_options?: mixed, // Provider-specific TTS options // Default: []
 *             stt_model?: string|Param, // Speech-to-text model name // Default: null
 *             stt_options?: mixed, // Provider-specific STT options // Default: []
 *         },
 *     }>,
 *     multi_agent?: array<string, array{ // Default: []
 *         orchestrator?: string|Param, // Service ID of the orchestrator agent
 *         handoffs?: array<string, list<scalar|Param|null>>,
 *         fallback?: string|Param, // Service ID of the fallback agent for unmatched requests
 *     }>,
 *     store?: array{
 *         azuresearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             api_version?: string|Param,
 *             index_name?: string|Param, // The name of the store will be used if the "index_name" option is not set
 *             http_client?: string|Param, // Default: "http_client"
 *             vector_field?: string|Param, // Default: "vector"
 *         }>,
 *         cache?: array<string, array{ // Default: []
 *             service?: string|Param, // Default: "cache.app"
 *             cache_key?: string|Param, // The name of the store will be used if the key is not set.
 *             strategy?: string|Param, // Default: "cosine"
 *         }>,
 *         chromadb?: array<string, array{ // Default: []
 *             client?: string|Param, // Default: "Codewithkyrian\\ChromaDB\\Client"
 *             collection?: string|Param,
 *         }>,
 *         clickhouse?: array<string, array{ // Default: []
 *             dsn?: string|Param,
 *             http_client?: string|Param,
 *             database?: string|Param,
 *             table?: string|Param,
 *         }>,
 *         cloudflare?: array<string, array{ // Default: []
 *             account_id?: string|Param,
 *             api_key?: string|Param,
 *             index_name?: string|Param,
 *             http_client?: string|Param, // Default: "http_client"
 *             dimensions?: int|Param, // Default: 1536
 *             metric?: string|Param, // Default: "cosine"
 *             endpoint?: string|Param,
 *         }>,
 *         elasticsearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             index_name?: string|Param,
 *             vectors_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *             similarity?: string|Param, // Default: "cosine"
 *             http_client?: string|Param, // Default: "http_client"
 *         }>,
 *         manticoresearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             table?: string|Param,
 *             field?: string|Param, // Default: "_vectors"
 *             type?: string|Param, // Default: "hnsw"
 *             similarity?: string|Param, // Default: "cosine"
 *             dimensions?: int|Param, // Default: 1536
 *             quantization?: string|Param,
 *         }>,
 *         mariadb?: array<string, array{ // Default: []
 *             connection?: string|Param,
 *             table_name?: string|Param,
 *             index_name?: string|Param,
 *             vector_field_name?: string|Param,
 *             setup_options?: array{
 *                 dimensions?: int|Param,
 *             },
 *             distance?: "cosine"|"euclidean"|"distance"|Param, // Distance metric to use for vector similarity search // Default: "euclidean"
 *         }>,
 *         meilisearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             index_name?: string|Param,
 *             http_client?: string|Param, // Default: "http_client"
 *             embedder?: string|Param, // Default: "default"
 *             vector_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *             semantic_ratio?: float|Param, // The ratio between semantic (vector) and full-text search (0.0 to 1.0). Default: 1.0 (100% semantic) // Default: 1.0
 *         }>,
 *         memory?: array<string, array{ // Default: []
 *             strategy?: string|Param,
 *         }>,
 *         milvus?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             database?: string|Param,
 *             collection?: string|Param,
 *             vector_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *             metric_type?: string|Param, // Default: "COSINE"
 *         }>,
 *         mongodb?: array<string, array{ // Default: []
 *             client?: string|Param, // Default: "MongoDB\\Client"
 *             database?: string|Param,
 *             collection?: string|Param,
 *             index_name?: string|Param,
 *             vector_field?: string|Param, // Default: "vector"
 *             bulk_write?: bool|Param, // Default: false
 *             setup_options?: array{
 *                 fields?: mixed, // Default: []
 *             },
 *         }>,
 *         neo4j?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             username?: string|Param,
 *             password?: string|Param,
 *             database?: string|Param,
 *             vector_index_name?: string|Param,
 *             node_name?: string|Param,
 *             vector_field?: string|Param, // Default: "embeddings"
 *             dimensions?: int|Param, // Default: 1536
 *             distance?: string|Param, // Default: "cosine"
 *             quantization?: bool|Param,
 *         }>,
 *         opensearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             index_name?: string|Param,
 *             vectors_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *             space_type?: string|Param, // Default: "l2"
 *             http_client?: string|Param, // Default: "http_client"
 *         }>,
 *         pinecone?: array<string, array{ // Default: []
 *             client?: string|Param, // Default: "Probots\\Pinecone\\Client"
 *             index_name?: string|Param,
 *             namespace?: string|Param,
 *             filter?: list<scalar|Param|null>,
 *             top_k?: int|Param,
 *         }>,
 *         postgres?: array<string, array{ // Default: []
 *             dsn?: string|Param,
 *             username?: string|Param,
 *             password?: string|Param,
 *             table_name?: string|Param,
 *             vector_field?: string|Param, // Default: "embedding"
 *             distance?: "cosine"|"inner_product"|"l1"|"l2"|Param, // Distance metric to use for vector similarity search // Default: "l2"
 *             lang?: string|Param, // Default: "english"
 *             dbal_connection?: string|Param,
 *             setup_options?: array{
 *                 vector_type?: string|Param, // Default: "vector"
 *                 vector_size?: int|Param, // Default: 1536
 *                 index_method?: string|Param, // Default: "ivfflat"
 *                 index_opclass?: string|Param, // Default: "vector_cosine_ops"
 *             },
 *         }>,
 *         qdrant?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             collection_name?: string|Param, // The name of the store will be used if the "collection_name" is not set
 *             http_client?: string|Param, // Default: "http_client"
 *             dimensions?: int|Param, // Default: 1536
 *             distance?: string|Param, // Default: "Cosine"
 *             async?: bool|Param, // Default: false
 *         }>,
 *         redis?: array<string, array{ // Default: []
 *             connection_parameters?: mixed, // see https://github.com/phpredis/phpredis?tab=readme-ov-file#example-1
 *             client?: string|Param, // a service id of a Redis client
 *             index_name?: string|Param,
 *             key_prefix?: string|Param, // Default: "vector:"
 *             distance?: "COSINE"|"L2"|"IP"|Param, // Distance metric to use for vector similarity search // Default: "COSINE"
 *         }>,
 *         s3vectors?: array<string, array{ // Default: []
 *             client?: string|Param, // Service reference to an existing S3VectorsClient
 *             configuration?: array<mixed>,
 *             vector_bucket_name?: string|Param,
 *             index_name?: string|Param,
 *             filter?: array<mixed>,
 *             top_k?: int|Param, // Default number of results to return // Default: 3
 *         }>,
 *         sqlite?: array<string, array{ // Default: []
 *             dsn?: string|Param,
 *             connection?: string|Param,
 *             table_name?: string|Param,
 *             strategy?: string|Param,
 *             vec?: bool|Param, // Default: false
 *             distance?: "cosine"|"L2"|Param, // Default: "cosine"
 *             vector_dimension?: int|Param, // Default: 1536
 *         }>,
 *         supabase?: array<string, array{ // Default: []
 *             http_client?: string|Param, // Service ID of the HTTP client to use // Default: "http_client"
 *             url?: string|Param,
 *             api_key?: string|Param,
 *             table?: string|Param,
 *             vector_field?: string|Param, // Default: "embedding"
 *             vector_dimension?: int|Param, // Default: 1536
 *             function_name?: string|Param, // Default: "match_documents"
 *         }>,
 *         surrealdb?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             username?: string|Param,
 *             password?: string|Param,
 *             namespace?: string|Param,
 *             database?: string|Param,
 *             table?: string|Param,
 *             http_client?: string|Param, // Default: "http_client"
 *             vector_field?: string|Param, // Default: "_vectors"
 *             strategy?: string|Param, // Default: "cosine"
 *             dimensions?: int|Param, // Default: 1536
 *             namespaced_user?: bool|Param,
 *         }>,
 *         typesense?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             collection?: string|Param,
 *             http_client?: string|Param, // Default: "http_client"
 *             vector_field?: string|Param, // Default: "_vectors"
 *             dimensions?: int|Param, // Default: 1536
 *         }>,
 *         weaviate?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             http_client?: string|Param, // Default: "http_client"
 *             collection?: string|Param, // The name of the store will be used if the "collection" is not set
 *         }>,
 *         vektor?: array<string, array{ // Default: []
 *             storage_path?: string|Param, // Default: "%kernel.project_dir%/var/share"
 *             dimensions?: int|Param, // Default: 1536
 *         }>,
 *     },
 *     message_store?: array{
 *         cache?: array<string, array{ // Default: []
 *             service?: string|Param, // Default: "cache.app"
 *             key?: string|Param, // The name of the message store will be used if the key is not set
 *             ttl?: int|Param,
 *         }>,
 *         cloudflare?: array<string, array{ // Default: []
 *             account_id?: string|Param,
 *             api_key?: string|Param,
 *             namespace?: string|Param,
 *             endpoint_url?: string|Param, // If the version of the Cloudflare API is updated, use this key to support it.
 *         }>,
 *         doctrine?: array{
 *             dbal?: array<string, array{ // Default: []
 *                 connection?: string|Param,
 *                 table_name?: string|Param, // The name of the message store will be used if the table_name is not set
 *             }>,
 *         },
 *         meilisearch?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             api_key?: string|Param,
 *             index_name?: string|Param,
 *         }>,
 *         memory?: array<string, array{ // Default: []
 *             identifier?: string|Param,
 *         }>,
 *         mongodb?: array<string, array{ // Default: []
 *             client?: string|Param, // Default: "MongoDB\\Client"
 *             database?: string|Param,
 *             collection?: string|Param,
 *         }>,
 *         pogocache?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             password?: string|Param,
 *             key?: string|Param,
 *         }>,
 *         redis?: array<string, array{ // Default: []
 *             connection_parameters?: mixed, // see https://github.com/phpredis/phpredis?tab=readme-ov-file#example-1
 *             client?: string|Param, // a service id of a Redis client
 *             endpoint?: string|Param,
 *             index_name?: string|Param,
 *         }>,
 *         session?: array<string, array{ // Default: []
 *             identifier?: string|Param,
 *         }>,
 *         surrealdb?: array<string, array{ // Default: []
 *             endpoint?: string|Param,
 *             username?: string|Param,
 *             password?: string|Param,
 *             namespace?: string|Param,
 *             database?: string|Param,
 *             table?: string|Param,
 *             namespaced_user?: bool|Param, // Using a namespaced user is a good practice to prevent any undesired access to a specific table, see https://surrealdb.com/docs/surrealdb/reference-guide/security-best-practices
 *         }>,
 *     },
 *     chat?: array<string, array{ // Default: []
 *         agent?: string|Param,
 *         message_store?: string|Param,
 *     }>,
 *     vectorizer?: array<string, array{ // Default: []
 *         platform?: string|Param, // Service name of platform // Default: "Symfony\\AI\\Platform\\PlatformInterface"
 *         model?: mixed,
 *     }>,
 *     indexer?: array<string, array{ // Default: []
 *         loader?: string|Param, // Service name of loader // Default: null
 *         source?: mixed, // Source identifier (file path, URL, etc.) or array of sources // Default: null
 *         transformers?: list<scalar|Param|null>,
 *         filters?: list<scalar|Param|null>,
 *         vectorizer?: scalar|Param|null, // Service name of vectorizer // Default: "Symfony\\AI\\Store\\Document\\VectorizerInterface"
 *         store?: string|Param, // Service name of store // Default: "Symfony\\AI\\Store\\StoreInterface"
 *     }>,
 *     retriever?: array<string, array{ // Default: []
 *         vectorizer?: scalar|Param|null, // Service name of vectorizer // Default: "Symfony\\AI\\Store\\Document\\VectorizerInterface"
 *         store?: string|Param, // Service name of store // Default: "Symfony\\AI\\Store\\StoreInterface"
 *     }>,
 * }
 * @psalm-type SurvosTablerConfig = array{
 *     icons?: array{
 *         prefix?: scalar|Param|null, // Default: "tabler"
 *         aliases?: array<string, scalar|Param|null>,
 *         presets?: array<string, array{ // Default: []
 *             icon?: scalar|Param|null,
 *             class?: scalar|Param|null, // Default: ""
 *         }>,
 *     },
 *     app?: array{
 *         code?: scalar|Param|null, // Default: "my-project"
 *         title?: scalar|Param|null, // Default: "My Project"
 *         description?: scalar|Param|null, // Default: ""
 *         abbr?: scalar|Param|null, // Default: "my<b>Project</b>"
 *         logo?: scalar|Param|null, // Default: null
 *         logo_small?: scalar|Param|null, // Default: null
 *         homepage_route?: scalar|Param|null, // Default: null
 *         homepage_url?: scalar|Param|null, // Default: null
 *         links?: array{
 *             github?: scalar|Param|null, // Default: null
 *             docs?: scalar|Param|null, // Default: null
 *             sponsor?: scalar|Param|null, // Default: null
 *             site?: scalar|Param|null, // Default: null
 *             contact?: scalar|Param|null, // Default: null
 *         },
 *         social?: array<string, scalar|Param|null>,
 *         meta?: array{
 *             og_image?: scalar|Param|null, // Default: null
 *             twitter_site?: scalar|Param|null, // Default: null
 *             theme_color?: scalar|Param|null, // Default: null
 *         },
 *         header?: array{
 *             locale_switcher?: bool|Param, // Default: true
 *             container?: scalar|Param|null, // Default: "container-fluid"
 *             auth?: array{
 *                 enabled?: bool|Param, // Default: true
 *                 show_login?: bool|Param, // Default: true
 *                 show_user_menu?: bool|Param, // Default: true
 *                 routes?: array{
 *                     login?: scalar|Param|null, // Default: "app_login"
 *                     logout?: scalar|Param|null, // Default: "app_logout"
 *                     register?: scalar|Param|null, // Default: "app_register"
 *                     profile?: scalar|Param|null, // Default: "app_profile"
 *                 },
 *             },
 *         },
 *     },
 *     favicon?: array{
 *         enabled?: bool|Param, // Serve a dynamic SVG favicon at /favicon.svg so apps get a useful icon without a favicon-generator workflow. // Default: true
 *         text?: scalar|Param|null, // 1-2 characters shown on the icon. Defaults to initials derived from app.code. // Default: null
 *         background?: scalar|Param|null, // Background fill, e.g. a distinct color per environment (prod/wip/dev/test). // Default: "#206bc4"
 *         foreground?: scalar|Param|null, // Text color. // Default: "#ffffff"
 *         shape?: "square"|"rounded"|"circle"|Param, // Default: "rounded"
 *     },
 *     routes?: array{
 *         home?: scalar|Param|null, // Default: "app_homepage"
 *         login?: scalar|Param|null, // Default: null
 *         logout?: scalar|Param|null, // Default: null
 *         register?: scalar|Param|null, // Default: null
 *         profile?: scalar|Param|null, // Default: null
 *         settings?: scalar|Param|null, // Default: null
 *         search?: scalar|Param|null, // Default: null
 *     },
 *     debug?: array{
 *         menu_slots?: bool|Param, // Default: false
 *         admin_toolbar?: bool|Param, // Render the orange admin menu toolbar (navbar_admin) for admins/debug. Defaults to the TABLER_ADMIN_TOOLBAR env var (1); developers can set TABLER_ADMIN_TOOLBAR=0 in .env.local to hide it. // Default: "%env(bool:TABLER_ADMIN_TOOLBAR)%"
 *     },
 *     options?: array{
 *         theme?: scalar|Param|null, // Default: "tabler"
 *         layout?: "horizontal"|"dashboard"|"vertical"|"condensed"|Param, // Default: "horizontal"
 *         dark_mode?: bool|Param, // Default: false
 *         show_locale_dropdown?: bool|Param, // Default: true
 *     },
 *     menu_options?: array<string, scalar|Param|null>,
 *     impersonate?: array<string, scalar|Param|null>,
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: ""
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 * }
 * @psalm-type SurvosFieldConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: ""
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 * }
 * @psalm-type SurvosImgproxyConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: ""
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     host?: scalar|Param|null, // Default: "%env(default::IMGPROXY_HOST)%"
 *     key?: scalar|Param|null, // Default: "%env(default::IMGPROXY_KEY)%"
 *     salt?: scalar|Param|null, // Default: "%env(default::IMGPROXY_SALT)%"
 *     presets?: array<string, array{ // Default: {"tiny":{"width":200,"height":200,"resize":"fit","quality":70,"format":"webp"},"thumb":{"width":400,"height":400,"resize":"fit","quality":80,"format":"webp"},"observe":{"width":512,"height":512,"resize":"fit","quality":80,"format":"webp"},"display":{"width":600,"height":400,"resize":"fit","quality":80,"format":"webp"},"archive":{"width":0,"height":0,"resize":"fit","quality":88,"format":"webp","strip_metadata":false}}
 *         width?: int|Param,
 *         height?: int|Param,
 *         resize?: scalar|Param|null, // Default: "fit"
 *         quality?: int|Param, // Default: null
 *         format?: scalar|Param|null, // Default: null
 *         strip_metadata?: bool|Param|null, // Default: null
 *     }>,
 * }
 * @psalm-type SurvosMediaConfig = array{
 *     default_locale?: scalar|Param|null, // Default: "en"
 *     cache_ttl?: scalar|Param|null, // Default: 3600
 *     media_server?: array{
 *         host?: scalar|Param|null, // Default: "https://media.wip"
 *         apiKey?: scalar|Param|null, // Default: null
 *         resize_path?: scalar|Param|null, // Default: "/media/{preset}/{id}"
 *     },
 *     presets?: array<string, array{ // Default: {"small":{"resize":"fill","width":192,"height":192},"medium":{"resize":"fit","width":400,"height":400},"large":{"resize":"fit","width":800,"height":800},"ai":{"resize":"fit","width":512,"height":512},"thumb":{"resize":"fit","width":300,"height":300}}
 *         resize?: scalar|Param|null, // Default: "fit"
 *         width?: int|Param,
 *         height?: int|Param,
 *     }>,
 *     providers?: array<string, array{ // Default: []
 *         enabled?: bool|Param, // Default: true
 *         api_key?: scalar|Param|null,
 *         api_secret?: scalar|Param|null,
 *         access_token?: scalar|Param|null,
 *         options?: list<mixed>,
 *     }>,
 * }
 * @psalm-type LiveComponentConfig = array{
 *     secret?: scalar|Param|null, // The secret used to compute fingerprints and checksums // Default: "%kernel.secret%"
 *     fetch_credentials?: "same-origin"|"include"|"omit"|Param, // The default fetch credentials mode for all Live Components ('same-origin', 'include', 'omit') // Default: "same-origin"
 * }
 * @psalm-type SurvosSearchConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: ""
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     default_adapter?: scalar|Param|null, // Default: "default"
 *     index_prefix?: scalar|Param|null, // Prefix applied to every Elasticsearch index name, once, by ElasticIndexNameResolver. Reuses MEILI_PREFIX so one app has one index namespace across both engines. Leaving it unset is an error the first time a name is resolved: bare index names share a flat cluster namespace with every other app on the node. Set it to an empty string to share deliberately. // Default: "%env(default::MEILI_PREFIX)%"
 *     adapters?: array<string, Param|string|array{ // Default: {"default":{"dsn":"doctrine://default"}}
 *         dsn?: scalar|Param|null,
 *     }>,
 *     default_hits_per_page?: int|Param, // Default: 24
 *     default_hits_per_page_choices?: list<int|Param>,
 * }
 * @psalm-type SurvosDocConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: "/doc"
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 *     user_provider?: scalar|Param|null, // Default: null
 *     user_class?: scalar|Param|null, // Default: "App\\Entity\\User"
 *     console?: array{
 *         include?: list<scalar|Param|null>,
 *     },
 * }
 * @psalm-type SurvosIiifConfig = array{
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: false
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: ""
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 * }
 * @psalm-type SurvosFetchConfig = array{
 *     persistent_cache_path?: scalar|Param|null, // SQLite file backing PersistentFetcher -- an app-controlled-TTL cache independent of what (if anything) the origin sends as Cache-Control/Expires. Deliberately outside %kernel.cache_dir% so it survives cache:clear. // Default: "%kernel.project_dir%/var/data/fetch_cache.db"
 * }
 * @psalm-type SurvosElasticConfig = array{
 *     spool_dir?: scalar|Param|null, // Where postFlush writes the ids awaiting reindex. // Default: "%kernel.project_dir%/var/elastic-spool"
 *     spool_enabled?: bool|Param, // Turn the Doctrine listener off for bulk imports that reindex explicitly afterwards. // Default: true
 *     async?: bool|Param, // Dispatch reindex work through Messenger. With this off (or with no bus installed) the listener writes a JSONL spool for elastic:spool:flush instead -- the right mode for bulk imports. // Default: true
 *     batch_size?: int|Param, // Ids per message. One huge flush becomes several bounded jobs. // Default: 500
 *     analysis?: array{ // Text analysis. Without it every text field uses the "standard" analyzer, which does no stemming and no accent folding -- searches work but are markedly worse, and any comparison against Meilisearch is unfair. index.analysis is a STATIC setting, so changing this needs elastic:index:rebuild.
 *         language?: scalar|Param|null, // Elasticsearch stemmer language: english, hungarian, spanish, german, french, ... Null leaves the default analyzer in place. // Default: null
 *         ascii_folding?: bool|Param, // Fold accents so "Kovacs" matches "Kovács". Applies only when a language is set. // Default: true
 *     },
 *     index_pattern?: scalar|Param|null, // Which cluster indices the admin page considers this app's, e.g. "kpa_*". The cluster index namespace is flat and shared by every app pointed at the node, so this is how the page finds indices this app owns but never declared -- a leftover from a rename, a locale variant. Defaults to survos_search.index_prefix + "*", so it tracks exactly what this app writes; set it only to widen or narrow that deliberately. // Default: null
 *     elasticvue_url?: scalar|Param|null, // Elasticvue (https://elasticvue.com) — the closest equivalent to the riccox Meilisearch UI. Point this at a self-hosted instance (docker run -p 8080:8080 cars10/elasticvue) or https://app.elasticvue.com. Null hides the menu link. Note that Elasticvue talks to Elasticsearch from the browser, so the node needs http.cors.enabled unless it is proxied. // Default: null
 *     kibana_url?: scalar|Param|null, // Kibana, if one is running. Null hides the menu link. // Default: null
 *     server_url?: scalar|Param|null, // The Elasticsearch node itself, for a direct link in the admin menu. Null hides it. // Default: null
 *     routes_enabled?: bool|Param, // Set false to manage this bundle's routes manually in your app. Bundles exposing sensitive routes (e.g. running console commands) should default this off. // Default: true
 *     route_prefix?: scalar|Param|null, // URL prefix applied to all routes from this bundle. // Default: "/admin/elastic"
 *     locale_prefix?: bool|Param, // Prepend {_locale} (constrained to kernel.enabled_locales) to this bundle's route prefix, e.g. /{_locale}/f instead of /f -- for bundles whose routes are meant to be shared/bookmarked, so the URL itself carries the locale instead of a query param. // Default: false
 * }
 * @psalm-type SurvosSchemaOrgConfig = array{
 *     pretty_print?: scalar|Param|null, // Indent the JSON-LD. Readable in dev, wasted bytes in prod, so it follows kernel.debug by default. Accepts a bool or a parameter reference. // Default: "%kernel.debug%"
 *     debug_panel?: scalar|Param|null, // Let schema_org_debug() render its panel. Follows kernel.debug by default; set false to keep the Twig call in the template but render nothing. // Default: "%kernel.debug%"
 * }
 * @psalm-type ConfigType = array{
 *     imports?: ImportsConfig,
 *     parameters?: ParametersConfig,
 *     services?: ServicesConfig,
 *     framework?: FrameworkConfig,
 *     doctrine?: DoctrineConfig,
 *     doctrine_migrations?: DoctrineMigrationsConfig,
 *     twig?: TwigConfig,
 *     twig_extra?: TwigExtraConfig,
 *     security?: SecurityConfig,
 *     monolog?: MonologConfig,
 *     stimulus?: StimulusConfig,
 *     twig_component?: TwigComponentConfig,
 *     survos_core?: SurvosCoreConfig,
 *     knp_menu?: KnpMenuConfig,
 *     survos_html_prettify?: SurvosHtmlPrettifyConfig,
 *     nelmio_cors?: NelmioCorsConfig,
 *     knpu_oauth2_client?: KnpuOauth2ClientConfig,
 *     survos_auth?: SurvosAuthConfig,
 *     survos_simple_datatables?: SurvosSimpleDatatablesConfig,
 *     fos_js_routing?: FosJsRoutingConfig,
 *     survos_crawler?: SurvosCrawlerConfig,
 *     survos_wiki?: SurvosWikiConfig,
 *     flysystem?: FlysystemConfig,
 *     liip_imagine?: LiipImagineConfig,
 *     ux_icons?: UxIconsConfig,
 *     inspector?: InspectorConfig,
 *     survos_meili?: SurvosMeiliConfig,
 *     survos_state?: SurvosStateConfig,
 *     zenstruck_messenger_monitor?: ZenstruckMessengerMonitorConfig,
 *     survos_ez?: SurvosEzConfig,
 *     survos_import?: SurvosImportConfig,
 *     survos_api_grid?: SurvosApiGridConfig,
 *     survos_js_twig?: SurvosJsTwigConfig,
 *     api_platform?: ApiPlatformConfig,
 *     ai?: AiConfig,
 *     survos_tabler?: SurvosTablerConfig,
 *     survos_field?: SurvosFieldConfig,
 *     survos_imgproxy?: SurvosImgproxyConfig,
 *     survos_media?: SurvosMediaConfig,
 *     live_component?: LiveComponentConfig,
 *     survos_search?: SurvosSearchConfig,
 *     survos_iiif?: SurvosIiifConfig,
 *     survos_fetch?: SurvosFetchConfig,
 *     survos_elastic?: SurvosElasticConfig,
 *     survos_schema_org?: SurvosSchemaOrgConfig,
 *     "when@dev"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         framework?: FrameworkConfig,
 *         doctrine?: DoctrineConfig,
 *         doctrine_migrations?: DoctrineMigrationsConfig,
 *         debug?: DebugConfig,
 *         twig?: TwigConfig,
 *         web_profiler?: WebProfilerConfig,
 *         twig_extra?: TwigExtraConfig,
 *         security?: SecurityConfig,
 *         monolog?: MonologConfig,
 *         maker?: MakerConfig,
 *         stimulus?: StimulusConfig,
 *         twig_component?: TwigComponentConfig,
 *         survos_core?: SurvosCoreConfig,
 *         knp_menu?: KnpMenuConfig,
 *         survos_html_prettify?: SurvosHtmlPrettifyConfig,
 *         nelmio_cors?: NelmioCorsConfig,
 *         knpu_oauth2_client?: KnpuOauth2ClientConfig,
 *         survos_auth?: SurvosAuthConfig,
 *         survos_simple_datatables?: SurvosSimpleDatatablesConfig,
 *         fos_js_routing?: FosJsRoutingConfig,
 *         survos_crawler?: SurvosCrawlerConfig,
 *         survos_command?: SurvosCommandConfig,
 *         survos_wiki?: SurvosWikiConfig,
 *         flysystem?: FlysystemConfig,
 *         liip_imagine?: LiipImagineConfig,
 *         ux_icons?: UxIconsConfig,
 *         doctrine_diagram?: DoctrineDiagramConfig,
 *         inspector?: InspectorConfig,
 *         survos_code?: SurvosCodeConfig,
 *         survos_meili?: SurvosMeiliConfig,
 *         survos_state?: SurvosStateConfig,
 *         zenstruck_messenger_monitor?: ZenstruckMessengerMonitorConfig,
 *         survos_ez?: SurvosEzConfig,
 *         survos_deployment?: SurvosDeploymentConfig,
 *         survos_import?: SurvosImportConfig,
 *         survos_api_grid?: SurvosApiGridConfig,
 *         survos_js_twig?: SurvosJsTwigConfig,
 *         api_platform?: ApiPlatformConfig,
 *         ai?: AiConfig,
 *         survos_tabler?: SurvosTablerConfig,
 *         survos_field?: SurvosFieldConfig,
 *         survos_imgproxy?: SurvosImgproxyConfig,
 *         survos_media?: SurvosMediaConfig,
 *         live_component?: LiveComponentConfig,
 *         survos_search?: SurvosSearchConfig,
 *         survos_doc?: SurvosDocConfig,
 *         survos_iiif?: SurvosIiifConfig,
 *         survos_fetch?: SurvosFetchConfig,
 *         survos_elastic?: SurvosElasticConfig,
 *         survos_schema_org?: SurvosSchemaOrgConfig,
 *     },
 *     "when@never"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         framework?: FrameworkConfig,
 *         doctrine?: DoctrineConfig,
 *         doctrine_migrations?: DoctrineMigrationsConfig,
 *         twig?: TwigConfig,
 *         twig_extra?: TwigExtraConfig,
 *         security?: SecurityConfig,
 *         monolog?: MonologConfig,
 *         stimulus?: StimulusConfig,
 *         twig_component?: TwigComponentConfig,
 *         survos_core?: SurvosCoreConfig,
 *         knp_menu?: KnpMenuConfig,
 *         survos_html_prettify?: SurvosHtmlPrettifyConfig,
 *         nelmio_cors?: NelmioCorsConfig,
 *         knpu_oauth2_client?: KnpuOauth2ClientConfig,
 *         survos_auth?: SurvosAuthConfig,
 *         survos_simple_datatables?: SurvosSimpleDatatablesConfig,
 *         fos_js_routing?: FosJsRoutingConfig,
 *         survos_crawler?: SurvosCrawlerConfig,
 *         survos_wiki?: SurvosWikiConfig,
 *         flysystem?: FlysystemConfig,
 *         liip_imagine?: LiipImagineConfig,
 *         ux_icons?: UxIconsConfig,
 *         inspector?: InspectorConfig,
 *         survos_meili?: SurvosMeiliConfig,
 *         survos_state?: SurvosStateConfig,
 *         zenstruck_messenger_monitor?: ZenstruckMessengerMonitorConfig,
 *         survos_ez?: SurvosEzConfig,
 *         survos_import?: SurvosImportConfig,
 *         survos_api_grid?: SurvosApiGridConfig,
 *         survos_js_twig?: SurvosJsTwigConfig,
 *         api_platform?: ApiPlatformConfig,
 *         ai?: AiConfig,
 *         survos_tabler?: SurvosTablerConfig,
 *         survos_field?: SurvosFieldConfig,
 *         survos_imgproxy?: SurvosImgproxyConfig,
 *         survos_media?: SurvosMediaConfig,
 *         live_component?: LiveComponentConfig,
 *         survos_search?: SurvosSearchConfig,
 *         survos_iiif?: SurvosIiifConfig,
 *         survos_fetch?: SurvosFetchConfig,
 *         survos_elastic?: SurvosElasticConfig,
 *         survos_schema_org?: SurvosSchemaOrgConfig,
 *     },
 *     "when@prod"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         framework?: FrameworkConfig,
 *         doctrine?: DoctrineConfig,
 *         doctrine_migrations?: DoctrineMigrationsConfig,
 *         twig?: TwigConfig,
 *         twig_extra?: TwigExtraConfig,
 *         security?: SecurityConfig,
 *         monolog?: MonologConfig,
 *         stimulus?: StimulusConfig,
 *         twig_component?: TwigComponentConfig,
 *         survos_core?: SurvosCoreConfig,
 *         knp_menu?: KnpMenuConfig,
 *         survos_html_prettify?: SurvosHtmlPrettifyConfig,
 *         nelmio_cors?: NelmioCorsConfig,
 *         knpu_oauth2_client?: KnpuOauth2ClientConfig,
 *         survos_auth?: SurvosAuthConfig,
 *         survos_simple_datatables?: SurvosSimpleDatatablesConfig,
 *         sentry?: SentryConfig,
 *         fos_js_routing?: FosJsRoutingConfig,
 *         survos_crawler?: SurvosCrawlerConfig,
 *         survos_wiki?: SurvosWikiConfig,
 *         flysystem?: FlysystemConfig,
 *         liip_imagine?: LiipImagineConfig,
 *         ux_icons?: UxIconsConfig,
 *         inspector?: InspectorConfig,
 *         survos_meili?: SurvosMeiliConfig,
 *         survos_state?: SurvosStateConfig,
 *         zenstruck_messenger_monitor?: ZenstruckMessengerMonitorConfig,
 *         survos_ez?: SurvosEzConfig,
 *         survos_import?: SurvosImportConfig,
 *         survos_api_grid?: SurvosApiGridConfig,
 *         survos_js_twig?: SurvosJsTwigConfig,
 *         api_platform?: ApiPlatformConfig,
 *         ai?: AiConfig,
 *         survos_tabler?: SurvosTablerConfig,
 *         survos_field?: SurvosFieldConfig,
 *         survos_imgproxy?: SurvosImgproxyConfig,
 *         survos_media?: SurvosMediaConfig,
 *         live_component?: LiveComponentConfig,
 *         survos_search?: SurvosSearchConfig,
 *         survos_iiif?: SurvosIiifConfig,
 *         survos_fetch?: SurvosFetchConfig,
 *         survos_elastic?: SurvosElasticConfig,
 *         survos_schema_org?: SurvosSchemaOrgConfig,
 *     },
 *     "when@test"?: array{
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         framework?: FrameworkConfig,
 *         doctrine?: DoctrineConfig,
 *         doctrine_migrations?: DoctrineMigrationsConfig,
 *         twig?: TwigConfig,
 *         web_profiler?: WebProfilerConfig,
 *         twig_extra?: TwigExtraConfig,
 *         security?: SecurityConfig,
 *         monolog?: MonologConfig,
 *         stimulus?: StimulusConfig,
 *         twig_component?: TwigComponentConfig,
 *         survos_core?: SurvosCoreConfig,
 *         knp_menu?: KnpMenuConfig,
 *         survos_html_prettify?: SurvosHtmlPrettifyConfig,
 *         nelmio_cors?: NelmioCorsConfig,
 *         knpu_oauth2_client?: KnpuOauth2ClientConfig,
 *         survos_auth?: SurvosAuthConfig,
 *         survos_simple_datatables?: SurvosSimpleDatatablesConfig,
 *         fos_js_routing?: FosJsRoutingConfig,
 *         survos_crawler?: SurvosCrawlerConfig,
 *         survos_command?: SurvosCommandConfig,
 *         survos_wiki?: SurvosWikiConfig,
 *         flysystem?: FlysystemConfig,
 *         liip_imagine?: LiipImagineConfig,
 *         ux_icons?: UxIconsConfig,
 *         doctrine_diagram?: DoctrineDiagramConfig,
 *         inspector?: InspectorConfig,
 *         survos_code?: SurvosCodeConfig,
 *         survos_meili?: SurvosMeiliConfig,
 *         survos_state?: SurvosStateConfig,
 *         zenstruck_messenger_monitor?: ZenstruckMessengerMonitorConfig,
 *         survos_ez?: SurvosEzConfig,
 *         survos_deployment?: SurvosDeploymentConfig,
 *         survos_import?: SurvosImportConfig,
 *         survos_api_grid?: SurvosApiGridConfig,
 *         survos_js_twig?: SurvosJsTwigConfig,
 *         api_platform?: ApiPlatformConfig,
 *         ai?: AiConfig,
 *         survos_tabler?: SurvosTablerConfig,
 *         survos_field?: SurvosFieldConfig,
 *         survos_imgproxy?: SurvosImgproxyConfig,
 *         survos_media?: SurvosMediaConfig,
 *         live_component?: LiveComponentConfig,
 *         survos_search?: SurvosSearchConfig,
 *         survos_doc?: SurvosDocConfig,
 *         survos_iiif?: SurvosIiifConfig,
 *         survos_fetch?: SurvosFetchConfig,
 *         survos_elastic?: SurvosElasticConfig,
 *         survos_schema_org?: SurvosSchemaOrgConfig,
 *     },
 *     ...<string, ExtensionType|array{ // extra keys must follow the when@%env% pattern or match an extension alias
 *         imports?: ImportsConfig,
 *         parameters?: ParametersConfig,
 *         services?: ServicesConfig,
 *         ...<string, ExtensionType>,
 *     }>
 * }
 */
final class App
{
    /**
     * @param ConfigType $config
     *
     * @psalm-return ConfigType
     */
    public static function config(array $config): array
    {
        /** @var ConfigType $config */
        $config = AppReference::config($config);

        return $config;
    }
}

namespace Symfony\Component\Routing\Loader\Configurator;

/**
 * This class provides array-shapes for configuring the routes of an application.
 *
 * Example:
 *
 *     ```php
 *     // config/routes.php
 *     namespace Symfony\Component\Routing\Loader\Configurator;
 *
 *     return Routes::config([
 *         'controllers' => [
 *             'resource' => 'routing.controllers',
 *         ],
 *     ]);
 *     ```
 *
 * @psalm-type RouteConfig = array{
 *     path: string|array<string,string>,
 *     controller?: string,
 *     methods?: string|list<string>,
 *     requirements?: array<string,string>,
 *     defaults?: array<string,mixed>,
 *     options?: array<string,mixed>,
 *     host?: string|array<string,string>,
 *     schemes?: string|list<string>,
 *     condition?: string,
 *     locale?: string,
 *     format?: string,
 *     utf8?: bool,
 *     stateless?: bool,
 * }
 * @psalm-type ImportConfig = array{
 *     resource: string,
 *     type?: string,
 *     exclude?: string|list<string>,
 *     prefix?: string|array<string,string>,
 *     name_prefix?: string,
 *     trailing_slash_on_root?: bool,
 *     controller?: string,
 *     methods?: string|list<string>,
 *     requirements?: array<string,string>,
 *     defaults?: array<string,mixed>,
 *     options?: array<string,mixed>,
 *     host?: string|array<string,string>,
 *     schemes?: string|list<string>,
 *     condition?: string,
 *     locale?: string,
 *     format?: string,
 *     utf8?: bool,
 *     stateless?: bool,
 * }
 * @psalm-type AliasConfig = array{
 *     alias: string,
 *     deprecated?: array{package:string, version:string, message?:string},
 * }
 * @psalm-type RoutesConfig = array{
 *     "when@dev"?: array<string, RouteConfig|ImportConfig|AliasConfig>,
 *     "when@never"?: array<string, RouteConfig|ImportConfig|AliasConfig>,
 *     "when@prod"?: array<string, RouteConfig|ImportConfig|AliasConfig>,
 *     "when@test"?: array<string, RouteConfig|ImportConfig|AliasConfig>,
 *     ...<string, RouteConfig|ImportConfig|AliasConfig>
 * }
 */
final class Routes
{
    /**
     * @param RoutesConfig $config
     *
     * @psalm-return RoutesConfig
     */
    public static function config(array $config): array
    {
        return $config;
    }
}
