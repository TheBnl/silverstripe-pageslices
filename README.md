# Page slice module for Silverstripe

This module provides a base `PageSlice` class on which new slices can be extended. See the basic included slices for an example implementation.

The module comes with an `PageSlicePage` that has the required set up and template to get started.

###Maintainers

[Bram de Leeuw](http://www.twitter.com/bramdeleeuw)

## Adding page slices to your custom pages

Easiest would be extending the `PageSlicePage` class, but you can also add an `$has_many` to the `PageSlice` class. Make sure that your using a GridField that has the `MultiClass` component (from the gridfieldextensions module) enabled.

This module supplies a GridField config set up that you can apply to your GridField. You can give it an array of classes you want to include on the page, otherwise it will take all available subclasses of `PageSlice`.

## Creating new page slices

To create new page slices simply extend the Page Slice base class. For some examples check out the included basic slices (ImageSlice and TextSlice) advanced examples are coming soon as separate modules! Feel free to publish your own page slice modules, documentation on this topic will follow.