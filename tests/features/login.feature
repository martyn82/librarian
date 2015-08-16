Feature: Log in using GitHub
  As a User
  In order to check out a Book
  I need to log in using GitHub

  Scenario: Log in using GitHub
    Given I am not logged in
     When I log in using GitHub
     Then I am logged in
