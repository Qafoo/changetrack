Feature: The Analyzer extracts method changes from a repository.
    The analyzer extracts which methods were changed in which way in each
    revision of a repository.

    Scenario: Assert some analysis results
        Given I have the repository "https://github.com/QafooLabs/Daemon.git"
         When I analyze the changes
         Then there are the following stats in revision "53b041d6cb921a9891a4d1fd750b372cdb8dfa13"
            | Class                   | Method  | Added | Removed |
            | QafooLabs\Daemon\Daemon | run     | 1     | 0       |
            | QafooLabs\Daemon\Daemon | doStart | 47    | 0       |
          And there are the following stats in revision "de6bbebd2b0a8f70af2182c47fe3cca106dcd072"
            | Class                   | Method         | Added | Removed |
            | QafooLabs\Daemon\Daemon | start          | 1     | 1       |
            | QafooLabs\Daemon\Daemon | waitRampUpTime | 1     | 6       |
