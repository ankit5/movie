# Domain

The Domain module suite lets you share users, content, and configuration across a group
of domains from a single installation and database.

For a full description of the module, visit the
[project page](https://www.drupal.org/project/domain).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/domain).


## Requirements

This module requires no modules outside of Drupal core.

The 2.0 version requires Drupal 9.5 and higher and is Drupal 10 compatible.

## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

## Included modules

- **Domain**
  The core module. Domain provides means for registering multiple domains within a single Drupal installation. It allows users to be assigned as domain administrators, provides a Block and Views display context, and creates default entity reference field for use by other modules.

- **Domain Access**
  Provides node access controls based on domains. (This module contains much of the Drupal 7 functionality). It allows users to be assigned as editors of content per-domain, sets content visibility rules, and provides Views integration for content.

- **Domain Alias**
  Allows multiple hostnames to be pointed to a single registered domain. These aliases can include wildcards (such as *.example.com) and may be configured to redirect to their canonical domain. Domain Alias also allows developers to register aliases per `environment`, so that different hosts are used consistently across development environments. See the README file for Domain Alias for more information.

- **Domain Config**
  Provides a means for changing configuration settings on a per-domain basis. See the README for Domain Config for more information.

- **Domain Content**
  Provides content overview pages on a per-domain basis, so that editors may review content assigned to specific domains. This module is a series of Views.

- **Domain Source**
  Allows content to be assigned a canonical domain when writing URLs. Domain Source will ensure that content that appears on multiple domains always links to one URL. See the module's README for more information.

## Implementation Notes

### Cross-domain logins

To use cross-domain logins, you must now set the **cookie_domain** value in **sites/default/services.yml**.

To do so, clone  `default.services.yml` to `services.yml` and change the `cookie_domain` value to match the root hostname of your sites. Note that  cross-domain login requires the sharing of a top-level domain, so a setting like `.example.com` will work for all `example.com` subdomains.

Example:

```
cookie_domain: '.example.com'
```

See `https://www.drupal.org/node/2391871`.


### Cross-Site HTTP requests (CORS)

As of Drupal 8.2, it's possible to allow a particular site to enable CORS for responses served by Drupal.

In the case of Domain, allowing CORS may remove AJAX errors caused when using some forms, particularly entity references, when the AJAX request goes to another domain.

This feature is not enabled by default because there are security consequences. See `https://www.drupal.org/node/2715637` for more information and instructions.

To enable CORS for all domains, copy `default.services.yml` to `services.yml` and enable the following lines:

```
   # Configure Cross-Site HTTP requests (CORS).
   # Read https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
   # for more information about the topic in general.
   # Note: By default the configuration is disabled.
  cors.config:
    enabled: false
    # Specify allowed headers, like 'x-allowed-header'.
    allowedHeaders: []
    # Specify allowed request methods, specify ['*'] to allow all possible ones.
    allowedMethods: []
    # Configure requests allowed from specific origins.
    allowedOrigins: ['*']
    # Sets the Access-Control-Expose-Headers header.
    exposedHeaders: false
    # Sets the Access-Control-Max-Age header.
    maxAge: false
    # Sets the Access-Control-Allow-Credentials header.
    supportsCredentials: false
```

The key here is setting `enabled` to `false`.

### Trusted host settings

If using the trusted host security setting in Drupal 8, be sure to add each domain and alias to the pattern list. For example:

```
$settings['trusted_host_patterns'] = array(
  '^.+\.example\.org$',
  '^myexample\.com$',
  '^myexample\.dev$',
  '^localhost$',
);
```

We **strongly encourage** the use of trusted host settings. When Domain issues a redirect, it will check the domain hostname against these settings. Any redirect that does not match the trusted host settings will be denied and throw an exception.

See `https://www.drupal.org/node/1992030` for more information.


### Configuring domain records

To create a domain record, you must provide the following information:

- A unique **hostname**, which may include a port. (Therefore, example.com and example.com:8080 are considered different.) The hostname may only contain alphanumeric characters, dashes, dots, and one colon. If you wish to use international domain names, toggle the `Allow non-ASCII characters in domains and aliases.` setting.
- A **machine_name** that must be unique. This value will be autogenerated and cannot be edited once created.
- A **name** to be used in lists of domains.
- A URL scheme, used for writing links to the domain. The scheme may be `http`, `https`, or `variable`. If `variable` is used, the scheme will be inherited from the server or request settings. This option is good if your test environments do not have secure certificates but your production environment does.
- A **status** indicating `active` or `inactive`. Inactive domains may only be viewed by users with permission to `view inactive domains` all other users will be redirected to the default domain (see below).
- The **weight** to be used when sorting domains. These values auto increment as new domains are created.
- Whether the domain is the **default** or not. Only one domain can be set as `default`. The default domain is used for redirects in cases where other domains are either restricted (inactive) or fail to load. This value can be reassigned after domains are created.

Domain records are **configuration entities**, which means they are not stored in the database nor accessible to Views by default. They are, however, exportable as part of your configuration.

### Domains and caching

If some variable changes are not picked up when the page renders, you may need add domain-sensitivity to the site's cache.

To do so, clone  `default.services.yml` to `services.yml` (if you have not already done so) and change the `required_cache_contexts` value to:

```YAML
    required_cache_contexts: ['languages:language_interface', 'theme', 'user.permissions', 'url.site']
```

The addition of `url.site` should provide the domain context that the cache layer requires.

For developers, see also the information in the Domain Alias README.

### Contributing

For Drupal 10+, you can use the [Domain DDEV](https://github.com/agentrickard/domain-ddev) project for getting started quickly. It includes all the tools described below.

If you file a pull request or patch, run the existing tests to check for failures. Writing additional tests will greatly speed completion, as I won't commit code without test coverage.

New tests should be written in PHPUnit as Functional, FunctionalJavascript, Kernel, or Unit tests.

To setup a proper environment locally, you need multiple or wildcard domains configured to point to your drupal instance. I use variants of `example.local` for local tests. See `DomainTestBase` for documentation. Domain testing should work with root hosts other than `example.com`, though we also expect to find the subdomains `one.*, two.*, three.*, four.*, five.*` in most test cases. See `DomainTestBase::domainCreateTestDomains()` for the logic.

When running tests, you normally need to be on the default domain.

### Code linting

We use (and recommend) [PHPCBF](https://phpqa.io/projects/phpcbf.html), [PHP Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer), and [phpstan](https://phpstan.org/) for code quality review.

The following commands are run before commit:

- `vendor/bin/phpcbf web/modules/contrib/domain --standard="Drupal,DrupalPractice" -n --extensions="php,module,inc,install,test,profile,theme"`
- `vendor/bin/phpcs web/modules/contrib/domain --standard="Drupal,DrupalPractice" -n --extensions="php,module,inc,install,test,profile,theme"`
- `vendor/bin/phpstan analyse web/modules/contrib/domain`


### Testing tools

We are using the following composer dev dependencies for local testing:

```
  "drupal/coder": "^8.3",
  "mglaman/drupal-check": "^1.4",
  "squizlabs/php_codesniffer": "^3.6",
```

Note that PHPCBF is installed as part of php_codesniffer.

### phpstan config

We use the following phpstan.neon file:

```
parameters:
  level: 2
  ignoreErrors:
    # new static() is a best practice in Drupal, so we cannot fix that.
    - "#^Unsafe usage of new static#"
  drupal:
    entityMapping:
      domain:
        class: Drupal\domain\Entity\Domain
        storage: Drupal\domain\DomainStorage
      domain_alias:
          class: Drupal\domain_alias\Entity\DomainAlias
          storage: Drupal\domain_alias\DomainAliasStorage

```

The drupal entityMapping is also provided by `entity_mapping.neon` in the project root, for use with other tests.


## Maintainers

- Ken Rickard - [agentrickard](https://www.drupal.org/u/agentrickard)
