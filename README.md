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