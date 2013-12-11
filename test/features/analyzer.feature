Feature: The Analyzer extracts method changes from a repository.
    The analyzer extracts which methods were changed in which way in each
    revision of a repository.

    Scenario: Assert some analysis results
        Given I have the repository
         When I analyze the changes
         Then there are the following stats in revision "53b041d6cb921a9891a4d1fd750b372cdb8dfa13"
            | Package          | Class  | Method  | Added | Removed |
            | QafooLabs\Daemon | Daemon | run     | 1     | 0       |
            | QafooLabs\Daemon | Daemon | doStart | 47    | 0       |
          And there are the following stats in revision "de6bbebd2b0a8f70af2182c47fe3cca106dcd072"
            | Package          | Class  | Method         | Added | Removed |
            | QafooLabs\Daemon | Daemon | start          | 1     | 1       |
            | QafooLabs\Daemon | Daemon | waitRampUpTime | 1     | 6       |

    Scenario: Analyze history only between given commits
        Given I have the repository
         When I analyze the changes from "166d2b3c3fa93027ee75cfd7be67347439e791f9" to "de6bbebd2b0a8f70af2182c47fe3cca106dcd072"
         Then there are no stats for revision "bb6f4f102ebaad2b8151bb44929eadce298e8ec9"
          And there are no stats for revision "55ab1318f4631ab138b68c8bb78d8204c2970b76"

    Scenario: Analyzing history of specific paths only
        Given I have the repository
         When I analyze the changes of paths "does-not-exist"
         Then there are no stats for revision "53b041d6cb921a9891a4d1fd750b372cdb8dfa13"
