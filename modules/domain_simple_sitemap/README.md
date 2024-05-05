## Domain Access Sitemap
========================

Domain Access Sitemap module generates sitemaps for active domains.

### Steps to generate map:
--------------------------

#### If you have no domains:
--------------------------
- Add a new domain (after adding a new domain a Sitemap Variant will created automatically)
- Allowing node access/source on this new domain:
    - Go to the edit's CT node edit form, for.ex admin/structure/types/manage/page
    - In the Simple XML Sitemap group please check that indexing is enabled for domain's variant
         "Index entities of type {Page} in variant {Domain Sitemap Variant}"
- Regenerate sitemap
    - Go to the admin/config/search/simplesitemap
    - Click the "Rebuild queue & generate" page
- your sitemap will be accessible in [you_new_domain]/sitemap.xml

#### If you already have domains:
--------------------------
- Go to the "Domain simple sitemap configuration" page (admin/config/domain/domain_simple_sitemap/config)
- Click to the "Generate domain's sitemap variants" button
- Allowing node access/source on this new domain:
    - Go to the edit's CT node edit form, for.ex admin/structure/types/manage/page
    - In the Simple XML Sitemap group please check that indexing is enabled for domain's variant
         "Index entities of type {Page} in variant {Domain Sitemap Variant}"
- Regenerate sitemap
    - Go to the admin/config/search/simplesitemap
    - Click the "Rebuild queue & generate" page
- your sitemap will be accessible in [you_new_domain]/sitemap.xml

### Configuration:
------------------
Domain Simple Sitemap have configuration setting page 
to chose between 'node access' and 'node source' in the sitemap listing filters.

Configuration page path:
/admin/config/domain/domain_simple_sitemap/config

### Dependencies:
-------------
- Domain Access
- Simple Sitemap
