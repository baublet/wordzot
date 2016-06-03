# WordZot
A powerful Zotero WordPress plugin

# What is it?
You have probably heard of ZotPress, a great WordPress Zotero plugin. I have used it, you should at least try it. I like it, but I don't love it. It's premised on a very narrow use case (including your bibliography in your page), doesn't have a whole lot of power features, doesn't let you theme your citations, and doesn't give you a way to add citations through the WordPress interface.

It also requires a hefty amount of Javascript that's entirely unnecessary for merely displaying citations that the plugin caches in plain text, anyway.

This plugin provides themable (via Twig) access to the Zotero API for integration into your WordPress themes. It's intended for power users and special use cases.

The Labor and Working-Class History Association will be using this plugin to manage member bibliographies, whereby members add their recent publications via a simple form with spam protection. An admin user may then see entries suggested for addition from specific forms and add them to the bibliography without ever leaving the WordPress administration panel.

# Roadmap
## Version 0.4
- [ ] Add a DB caching layer

## Version 0.3
- [ ] Advanced shortcode options
  - [ ] Automatic pagination
  - [ ] Searches
  - [ ] Multiple tags
  - [ ] Logic

## Version 0.2
- [x] More shortcode options
  - [x] Show items from a tag
  - [x] Show items from a group
  - [x] Show items from a collection

- [ ] Add support for user-submitted items

## Version 0.1
- [x] Basic Zotero integration
  - [x] Allow user to configure the plugin by adding a Zotero key
  - [x] Check the validity and permissions of the Zotero api key

- [x] Basic shortcode options
  - [x] Show all items from a user

- [x] Fetch references from a Zotero account/group
  - [x] Allow the user to do basic operations
    - [x] Fetch a user's collections
    - [x] Fetch a user's groups
    - [x] Fetch a group's collections
    - [x] Fetch a user's tags
    - [x] Fetch a group's tags
    - [x] Basic item fetching
    - [x] Formatting all of the above for dev-friendly operations

- [x] Advanced shortcode options
  - [x] Including/excluding types

- [x] Setup a cache these so we don't have to hammer the API
  - [x] Steps for fetching before displaying
    - [x] If it's already cached, just use the information in the local db
    - [x] Is the cache old? Dump it, if so and go to next step. Otherwise jump to display
    - [x] Fetch items from the API
    - [x] Put them in the local cache
    - [x] Display them

- [x] Decouple phpZot so that I can add it as a submodule and its own repo later
- [x] Incorporate Twig templating for displaying references
  - [x] Setup backend admin options for this (we won't be doing inline templates)
  - [x] Template groups
    - [x] Default - the fallbacks and default templates - can't delete it
    - [x] Users can create their own groups, however
    - [x] Each group has a separate template for each citation type

  - [x] Use Twig to render the citations
  - [x] If no template in the group? Use default. No default for that type? Don't render it
