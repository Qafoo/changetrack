ChangeTrack
===========

[![image](https://travis-ci.org/Qafoo/changetrack.png)](https://travis-ci.org/Qafoo/changetrack)

[![image](https://scrutinizer-ci.com/g/Qafoo/changetrack/badges/quality-score.png?s=15cd5a7c521b0e59db4c537ff8525381039cf013)](https://scrutinizer-ci.com/g/Qafoo/changetrack/)

[![Stories in Ready](https://badge.waffle.io/Qafoo/changetrack.png?label=ready)](https://waffle.io/Qafoo/changetrack)

The Qafoo ChangeTrack tool allows you to track changes in classes and
methods throughout the history of a PHP project. One application for
this analysis is to detect artifacts that are changed most frequently
and especially most frequently due to bugs.

Quick Start
-----------

To get started, download a [recent release
PHAR](https://github.com/Qafoo/changetrack/releases).

To create the base analysis file (changes by methods) try:

    $ php changetrack.phar analyze -o analysis.xml -p https://github.com/Qafoo/changetrack.git

This will analyze the history of ChangeTrack itself, store the resulting XML in
`analysis.xml` and display its progress to you.

On that basis, you can analyze bugfix/feature frequencies per method using:

    $ php changetrack.phar calculate analysis.xml > label-frequency.xml

This will use the standard regex label-provider to assign change labels and
calculate the frequency for each method.

Also on basis of the `analysis.xml` you can calculate frequent item sets among
method changes, using:

    $ php changetrack.phar frequent-item-sets -s 0.05 analysis.xml > fis.xml

That yields you a `fis.xml` with methods that are commonly changed together in
5% or more commits.

Please refer to the following sections for a deeper insight and to the
commandline help commands.

Introduction
------------

The analysis is performed in multiples steps. Currently supported are:

1.  Analyze which classes/methods are affected by a revision.
2.  Analyze which classes/methods are most frequently affected by
    changes.
3.  Analyze which methods are commonly edited together.

The split into multiple steps allows you to

a)  Cache the result of a step to apply multiple other analysis steps on
    top.
b)  Easily apply analysis processes which are currently not supported by
    ChangeTrack.

The different analysis types are combined in the single `track` shell
command. Executing it using:

    $ src/bin/track

Will provide you with a list of sub-commands to perform actual analysis.

Installation
------------

To install the tool, download a [PHAR
release](https://github.com/Qafoo/changetrack/releases) or obtain a checkout
from Github, get [Composer](http://getcomposer.org/doc/00-intro.md) and
execute:

    $ composer install

After that, you can use ChangeTrack from `src/bin/track`.

Commands
--------

In following, the currently implemented analysis commands are described
in further detail.

### analyze

The `analyze` command tracks changes of a PHP project throughout its
version history (currently only Git is supported). The result is an XML
document, that describes the version history in terms of class/method
changes. For example, here is a part of the
[Twig](https://github.com/fabpot/Twig) project history:

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

Each revision (which contains a change to a class/method) is reflected
in a `<changeSet />` which gives you the version (hash) and the commit
message. Contained is information about all method changes (structured
by package and class name) and statistics on the number of added/removed
lines.

You can restrict the history to be analyzed by a start and end commit
through command line parameters. This makes some sense, since analysis
is quite expensive: Every revision needs to be checked out and static
analysis is performed to detect which artifacts are affected by the
change.

The raw output of the `analyze` command is not really useful, yet. You
should apply the `calculate` command to it.

### calculate

The `calculate` command analyzes, how often an artifact (i.e. method) is
affected by what kind of change (e.g. *bug* or *feature*). To do so, it
attempts to assign a *label* to each commit in the project history and
counts per method, how often a specifically labeled commit touched it.
To do this, the `calculate` command operates on the output of the
`analyze` command (either by specifying the input file as an argument on
the shell or by just piping it from STDIN).

In order to find a label for a commit, `calculate` commonly analyzes the
commit message for keywords (e.g. "implemented" or "fixed"), but can
also utilize other methods (e.g. checking for a Github issue reference
and looking up its assigned labels through the Github API). The label
for a commit is then assigned to each artifact that was changed in that
commit.

Since every project has a different style of crafting its commit
messages, you can define how labels are determined through a dedicated
`config.yml` file (`-c` option), for example. The default configuration
(chosen if now `-c` option is present) looks like this:

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

This specific `config.yml` defines a chain of label providers. For a
commit, each of the defined label providers (2 x `regex`, 1 x `default`)
is asked to provide a label. If a provider can provide a label, this one
is chosen.

The `regex` label provider tries to match `pattern` against the commit
message and returns the defined `label` if it found a match. The
`default` provider always returns the defined `label` and therefore
finishes the chain.

An example output gathered using the default regex configuration (from
above) for the [Twig](https://github.com/fabpot/Twig) repository is
shown below:

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

As you can see, each method that occurrs in the history is listed
together with the number of changes with a specific label. So, the
`calculate` command found *17* commits with the label *misc* and *1*
commit with the label *fix* for the method
`Twig_Environment::loadTemplate()`.

Besides the *regex* and *default* label providers, there's a *Github
issue* label provider available, which uses your projects issue labels
to determine a change label. An example configuration for
[vfsStream](https://github.com/mikey179/vfsStream) project looks like
this:

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

The first provider in the chain tries to extract a Github issue
reference (e.g. `#23`) from the commit message. If that is available,
the Github API is used to determine labels for that issue. The labels
provded by Github are then mapped to local labels (which are the same
here).

If that provider does not find a label, 3 regexes are tried after each
other. Finally, if none of the previous providers found a label, the
default provider sets the *misc* label.

### frequent-item-sets

The `frequent-item-sets` command performs a [Frequent Item Set
Analysis](https://en.wikipedia.org/wiki/Association_rule_learning), taking
method changes as items and commits as transactions. That means, it
calculates  which methods are frequently edited together in your project.

The calculation is performed on basis of the results of the `analysis`
command using:

    $ track frequent-item-sets your_analysis_result.xml

The output of this command is an XML presenting all frequent item sets that
could be found in `your_analysis_result.xml`, for example:

    <?xml version="1.0" encoding="UTF-8"?>
    <frequentItemSets>
        <!-- ... -->
        <itemSet support="0.011070110701107">
            <item>
                <packageName><![CDATA[]]></package>
                <className><![CDATA[Twig_Node_Module]]></class>
                <methodName><![CDATA[__construct]]></method>
                <itemType><![CDATA[method]]></itemType>
            </item>
            <item>
                <packageName><![CDATA[]]></package>
                <className><![CDATA[Twig_Parser]]></class>
                <methodName><![CDATA[parse]]></method>
                <itemType><![CDATA[method]]></itemType>
            </item>
        </itemSet>
        <!-- ... -->
    </frequentItemSets>

The example shows an extract with a single item set that consists of two
items: The methods `Twig_Node_Module::__construct()` and `Twig_Parser::parse()`
were edited together in more that 1% of the commits in the
[Twig](https://github.com/fabpot/Twig) repository.

You can apply the option `-i` to get a frequent item analysis on basis of
classes instead of methods:

    $ track frequent-item-sets -i class your_analysis_result.xml

Note that frequent item set analysis is typically applied to databases where
the items do not change, which does not apply to software projects (e.g. new
classes are created). Therefore, the typical setting of a high support value
cannot hold here. Instead, in case of Twig 1% (0.01) is a sensible first
support value to retrieve initial results.

The support value should be identified for each project individually. 0.5 is a
good starting point, but most likely you need to go down to 0.1 or even 0.01.
It can also make sense to only analyze a fraction of the changes (e.g. of the
past year), if your project underwent a major refactoring.

Roadmap
-------

The ChangeTrack tool is currently in a very early alpha state. It has
only been run against a couple of repositories and it is expected that
you find quite some bugs. However, here are some of the features which
would make sense in the future:

-   Support different version control systems (e.g. SVN)
-   Performance improvements
-   Further label providers (e.g. by Jira issue labels)
-   Additional analysis, e.g. frequent item sets to determine coupling

Please add your ideas for additional features to the Github issue
tracker and possibly provide a pull request.
