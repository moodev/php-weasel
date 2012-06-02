PHP Weasel
==========

One day this will be a nice general purpose set of marshalling libraries along the same lines as Jackson. But
 for PHP.

It is configured using "annotations" in docblock comments, and as a result also includes a library for parsing those.

At present:
  * The JSON implementation appears to work for both directions;
  * The Xml implementation only works for deserialization;
  * It needs a top level "vendor" namespace, but I can't think of one;
  * Pretty much nothing is tested;
  * The only example of this being used is in a private repo;
  * The logger is a joke;
  * There is no documentation.

