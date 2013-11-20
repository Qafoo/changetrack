===========
ChangeTrack
===========

The Qafoo ChangeTrack tool allows you to track changes in classes and methods
throughout the history of a PHP project. One application for this analysis is
to detect artifacts that are changed most frequently and especially most
frequently due to bugs.

The analysis is performed in multiples steps. Currently supported are:

1. Analyze which classes/methods are affected by a revision.
2. Analyze which classes/methods are most frequently affected by changes.

The split into multiple steps allows you to

a) Cache the result of a step to apply multiple other analysis steps on top.
b) Easily apply analysis processes which are currently not supported by
   ChangeTrack.

The different analysis types are combined in the single ``track`` shell
command. Executing it using::

    $ src/bin/track

Will provide you with a list of sub-commands to perform actual analysis.

------------
Installation
------------

To install the tool obtain a copy from Github, get [Composer](http://getcomposer.org/doc/00-intro.md) 
and execute::

    $ composer install

After that, you can use ChangeTrack from ``src/bin/track``.

--------
Commands
--------

In following, the currently implemented analysis commands are described in
further detail.

analyze
=======

The ``analyze`` command tracks changes of a PHP project throughout its version
history (currently only Git is supported). The result is an XML document, that
describes the version history in terms of class/method changes. For example,
here is a part of the [Twig](https://github.com/fabpot/Twig) project history::

    <changes repository="https://github.com/fabpot/Twig">
      <!-- ... -->
      <changeSet revision="92bbc7ee405f5635f4647040d883dbd77d1ac7da" message="made a small optimization to for loop when no else clause exists&#10;git-svn-id: http://svn.twig-project.org/trunk@32 93ef8e89-cb99-4229-a87c-7fa0fa45744b&#10;">
        <package name="">
          <class name="Twig_Node_For">
            <method name="compile">
              <added>15</added>
              <removed>3</removed>
            </method>
          </class>
        </package>
      </changeSet>
      <!-- ... -->
    </changes>

Each revision (which contains a change to a class/method) is reflected in a
``<changeSet />`` which gives you the version (hash) and the commit message.
Contained is information about all method changes (structured by package and
class name) and statistics on the number of added/removed lines.

You can restrict the history to be analyzed by a start and end commit through
command line parameters. This makes some sense, since analysis is quite
expensive: Every revision needs to be checked out and static analysis is
performed to detect which artifacts are affected by the change.

The raw output of the ``analyze`` command is not really useful, yet. You should
apply the ``calculate`` command to it.

calculate
=========

The ``calculate`` command operates on the output of the ``analyze`` command. It
calculates statistics on how often a certain method is affected by changes. In
order to get meaningful statistics, you can provide your own mechanism to
determine if a change fixed a bug or implemented a feature.

Currently, regular expressions against the commit message are supported to
provide a label for each commit. You can define this configuration through a
dedicated ``config.yml`` file (``-c`` option), for example::

    revision_label_provider:
        chain:
            - regex:
                pattern: '(fixed)i'
                label:   'fix'
            - regex:
                pattern: '(implemented)i'
                label:   'implement'
            - default:
                label:   'misc'

This is the default config, which lets the defined regex be applied
sequentially and select the label of the first matching one. So, if the message
of a commit matches ``(fixed)i``, the label ``fix`` is issued. If none of the
regex matches, the default label ``misc`` is used.

An example output gathered using the default regex configuration from
above from the Twig repository is shown below::

    <stats  repository="https://github.com/fabpot/Twig">
      <package name="">
        <!-- ... -->
        <class name="Twig_Environment">
          <!-- ... -->
          <method name="loadTemplate">
            <stats>
              <count label="misc">17</count>
              <count label="fix">1</count>
            </stats>
          </method>
        </class>
      </package>
    </stats>

As you can see, each method that occurrs in the history is listed together
with the number of changes with a specific label. You can now easily e.g. check
for the methods which are most frequently affected by bugs.

Besides the *regex* and *default* label providers, there's a *Github issue*
label provider available, which uses your projects issue labels to determine a
change label. An example configuration for [vfsStream](https://github.com/mikey179/vfsStream) project looks like
this::

    revision_label_provider:
        chain:
            - github:
                issue_url_template: https://api.github.com/repos/mikey179/vfsStream/issues/:id/labels?access_token=<github_oauth_token>
                label_map:
                    bug:        bug
                    feature:    feature
            - regex:
                pattern: '(implemented)i'
                label:   'feature'
            - regex:
                pattern: '(fix)i'
                label:   'bug'
            - regex:
                pattern: '(merged)i'
                label:   'merge'
            - default:
                label:   'misc'
   
This configuration defines a chain of label providers, which chooses the first
one that can provide a label for a given commit. The first provider in the
chain tries to extract a Github issue reference from the commit message. If
that is available, the Github API is used to determine labels. The labels
provded by Github are then mapped to local labels (which are the same here).

If that provider does not find a label, 3 regexes are tried after each other.
Finally, if none of the previous providers found a label, the default provider
sets the *misc* label.

-------
Roadmap
-------

The ChangeTrack tool is currently in a very early alpha state. It has only been
run against a couple of repositories and it is expected that you find quite
some bugs. However, here are some of the features which would make sense in the
future:

- Support different version control systems (e.g. SVN)
- Performance improvements
- Further label providers (e.g. by Jira issue labels)
- Additional analysis, e.g. frequent item sets to determine coupling

Please add your ideas for additional features to the Github issue tracker and
possibly provide a pull request.

..
   Local Variables:
   mode: rst
   fill-column: 79
   End: 
   vim: et syn=rst tw=79
