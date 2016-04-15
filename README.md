# WordZot
A powerful Zotero WordPress plugin

# What is it?
You have probably heard of ZotPress, a great WordPress Zotero plugin. I have used it, you should at least try it. I like it, but I don't love it. It's premised on a very narrow use case (including your bibliography in your page), doesn't have a whole lot of power features, doesn't let you theme your citations, and doesn't give you a way to add citations through the WordPress interface.

It also requires a hefty amount of Javascript that's entirely unnecessary for merely displaying citations that the plugin caches in plain text, anyway.

This plugin provides themable (via Twig) access to the Zotero API for integration into your WordPress themes. It's intended for power users and special use cases.

The Labor and Working-Class History Association will be using this plugin to manage member bibliographies, whereby members add their recent publications via a simple form with spam protection. An admin user may then see entries suggested for addition from specific forms and add them to the bibliography without ever leaving the WordPress administration panel.

# Roadmap
## Version 0.1
- [x] Basic Zotero integration
  - [x] Allow user to configure the plugin by adding a Zotero key
  - [x] Check the validity and permissions of the Zotero api key

- [ ] Fetch references from a Zotero account/group
  - [ ] Allow the user to do basic operations
    - [ ] Fetch a user's collections
    - [x] Fetch a user's groups
    - [ ] Fetch a group's collections

  - [ ] Setup a backend database to cache these so we don't have to hammer the API
  - [ ] Steps for fetching before displaying
    - [ ] If it's already cached, just use the information in the local db
    - [ ] Clear the database of all references matching earlier versions of this request
    - [ ] Fetch items from the API
    - [ ] Put them in the local db
    - [ ] Display them

- [ ] Incorporate Twig templating for displaying references
  - [ ] Setup backend admin options for this (we won't be doing inline templates)
  - [ ] Template groups
    - [ ] Default - the fallbacks and default templates - can't delete it
    - [ ] Users can create their own groups, however
    - [ ] Each group has a separate template for each citation type

  - [ ] Use Twig to render the citations
  - [ ] If no template in the group? Use default. No default for that type? Show an error
