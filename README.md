# Page slice module for Silverstripe

This module provides a base `PageSlice` class on which new slices can be extended.
A `PageContentSlice` is included by default, this slice holds the parent's content.

## Installation
To add page slices to your page simply include the `PageSlicesExtension` to your page. 

### Setting up default slices
This module comes with some config setting by which default slices can be set up. For example:

```yaml
Page:
  extensions:
    - PageSlicesExtension
  default_slices:
    - PageContentSlice
PageSlice:
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

###Maintainers

[Bram de Leeuw](http://www.twitter.com/bramdeleeuw)

## License

Copyright (c) 2016, Bram de Leeuw
All rights reserved.

All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

 * Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
 * The name of Bram de Leeuw may not be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.