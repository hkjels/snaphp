# features/Route.feature
Feature: Route
  In order to output a proper page
  As a browser
  I need to normalize the url



Scenario: Output 5 normalized paths
  Given The first segment is "test"
  And I have a param named "foo"
  And I have a param named "bar"
  When I run "normalizePath"
  Then I should get:
    """
    bar
    foo
    """
