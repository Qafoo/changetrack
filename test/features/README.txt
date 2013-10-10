======================
README for Behat Tests
======================

Behat tests currently run against a Git repository which is directly cloned
from Github. While this makes it convenient for many people to run the tests
out of the box, there are cases where you want to use a local copy of that
repository instead. To do so, export the following parameter before runing
Behat::

    $ export BEHAT_PARAMS="context[parameters][repositoryUrl]=/your/path"


..
   Local Variables:
   mode: rst
   fill-column: 79
   End: 
   vim: et syn=rst tw=79
