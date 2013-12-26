Feature: The FISCalculator determines frequent item sets from analyzed changes.

    Scenario: Assert some frequent item sets
        Given I have analyzed the following changes
            | Revision | Package | Class | Method | Added | Removed |
            | 1        | A       | X     | a      | 1     | 0       |
            | 1        | A       | Y     | a      | 1     | 0       |
            | 1        | A       | Y     | b      | 1     | 0       |
            | 2        | A       | X     | a      | 1     | 0       |
            | 2        | A       | Y     | a      | 1     | 0       |
            | 3        | A       | X     | a      | 1     | 0       |
            | 3        | A       | X     | c      | 1     | 0       |
            | 4        | A       | X     | a      | 1     | 0       |
            | 4        | A       | Y     | b      | 1     | 0       |
         When I calculate frequent item sets with min support "0.5"
         Then I have the following frequent item sets
            | Item set            | Support |
            | A::X::a, A::Y::b    | 0.5     |
            | A::X::a, A::Y::a    | 0.5     |
            | A::X::a             | 1.0     |
            | A::Y::a             | 0.5     |
            | A::Y::b             | 0.5     |
