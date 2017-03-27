# Page slice module for Silverstripe

This module provides a base `PageSlice` class on which new slices can be extended.
A `PageContentSlice` is included by default, this slice holds the parent's content.
 
## Installation
To add page slices to your page simply include the `PageSlicesExtension` to your page. 

### Setting up default slices
Default slices are slices set up by the developer in the config that are installed on createion of a page or object after write. This module comes with some config setting by which default slices can be set up. For example:

```yaml
Page:
  extensions:
    - Broarm\Silverstripe\PageSlices\PageSlicesExtension
  default_slices:
    - PageContentSlice
Broarm\Silverstripe\PageSlices\PageSlice:
  default_slices_exceptions:
    - Blog
```
With the above config all pages would get the `PageContentSlice` by default except for `Blog` pages.

The config stacks, so if you would like to add a banner slice to blog posts by default you could add the following to the config:

```yaml
BlogPost:
  default_slices:
    - BannerSlice
    # By adding the content slice you can control the sort order
    # Otherwise stacked slices will be appended to the list
    - PageContentSlice
```

### PageContentSlice template hierarchy

The Page content slices looks for it's template in a similar manner as the Page class.
For example, a `PageContentSlice` added to a `BlogPost` would prefer the `BlogPostContentSlice.ss` template above the `PageContentSlice.ss`.
It iterates trough the class hierarchy until it stumbles upon a usable template.

#### Note
The module is namespaced except for the `PageContentSlice`, this is because the `GridFieldAddNewMultiClass` does not support namespaced classes yet.
For your own PageSlices take care to not namespace them (yet!). 

###Maintainers

[Bram de Leeuw](http://www.twitter.com/bramdeleeuw)
