# WordZot
A powerful Zotero WordPress plugin

# What is it?
You have probably heard of ZotPress, a great WordPress Zotero plugin. I have used it, you should at least try it. I like it, but I don't love it. It's premised on a very narrow use case (including your bibliography in your page), doesn't have a whole lot of power features, doesn't let you theme your citations, and doesn't give you a way to add citations through the WordPress interface.

It also requires a hefty amount of Javascript that's entirely unnecessary for merely displaying citations that the plugin caches in plain text, anyway.

This plugin provides themable (via Twig) access to the Zotero API for integration into your WordPress themes. It's intended for power users and special use cases.

The Labor and Working-Class History Association will be using this plugin to manage member bibliographies, whereby members add their recent publications via a simple form with spam protection. An admin user may then see entries suggested for addition from specific forms and add them to the bibliography without ever leaving the WordPress administration panel.

# Roadmap
# How I want Templates to Work
Templates will be available in the backend to style the display of one's citations. There will be several built-in options for this (e.g., Chicago, MLA, APA, etc.), but the user will have a drop-down box of all types of items that are available in their library to theme. This way, they can theme them to however they want.

Built-in templates will use hard-coded, inline styles, but we're using Twig here so people can edit that and make it slimmer if they so choose.

Users will be able to create an infinite number of template groups so that they can style the same references in different ways in different places. But there will always be a default template group that will be the fallback when unknown template groups are entered, or when a template group doesn't have a template defined for a particular item type. WordZot will fall back to that item's type in the default subgroup, and to the "default default" if that type isn't set on the default template.

# Caching
I'm still thinking through how I should cache. I think I want to cache by request (and thus in chunks) rather than storing individual items in a custom table. I'd like to use as few custom database options as possible, even if that means storing a serialized object as a WordPress option.

I'm thinking at the moment of preferring the filesystem, but falling back to WordPress options if the user prefers it or the cache isn't writable.

## Version 0.1
- [x] Basic Zotero integration
  - [x] Allow user to configure the plugin by adding a Zotero key
  - [x] Check the validity and permissions of the Zotero api key

- [ ] Fetch references from a Zotero account/group
  - [x] Allow the user to do basic operations
    - [x] Fetch a user's collections
    - [x] Fetch a user's groups
    - [x] Fetch a group's collections
    - [x] Fetch a user's tags
    - [x] Fetch a group's tags
    - [x] Basic item fetching
    - [x] Formatting all of the above for dev-friendly operations

  - [ ] Basic shortcode options
    - [ ] Show items from a tag
    - [ ] Show items from a group
    - [ ] Show items from a collection

  - [ ] Advanced shortcode options
    - [ ] Automatic pagination
    - [ ] Searches
    - [ ] Multiple tags
    - [ ] Logic

- [ ] Setup a backend database to cache these so we don't have to hammer the API
  - [ ] Steps for fetching before displaying
    - [ ] If it's already cached, just use the information in the local db
    - [ ] Is the cache old? Dump it, if so and go to next step. Otherwise jump to display
    - [ ] Fetch items from the API
    - [ ] Put them in the local DB
    - [ ] Display them

- [ ] Decouple phpZot so that I can add it as a submodule and its own repo later
- [ ] Incorporate Twig templating for displaying references
  - [ ] Setup backend admin options for this (we won't be doing inline templates)
  - [ ] Template groups
    - [ ] Default - the fallbacks and default templates - can't delete it
    - [ ] Users can create their own groups, however
    - [ ] Each group has a separate template for each citation type

  - [ ] Use Twig to render the citations
  - [ ] If no template in the group? Use default. No default for that type? Show an error
