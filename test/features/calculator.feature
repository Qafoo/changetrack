Feature: The Calculator calculates which methods are most affected by bugs.
    The Calculator should calculate which methods are affected by which
    type of change, most interestingly by bug fixing.

    Scenario: Assert calculation of bug statistics
        Given I have the repository "https://github.com/QafooLabs/Daemon.git"
         When I analyze the changes
          And I calculate the stats
         Then I have the following stats
            | Class                   | Method | Change Type | Value |
            | QafooLabs\Daemon\Daemon | start  | fix         | 1     |
            | QafooLabs\Daemon\Daemon | start  | implement   | 2     |
            | QafooLabs\Daemon\Daemon | start  | misc        | 3     |
