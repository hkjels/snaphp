
# features/Route.feature

Feature: Normalize routes
  In order to route a request
  As a router
  I need to normalize paths to regular expressions

  Scenario: String to regEx
    Given A string '/ping/pong/'
    Then I should get:
      "^\/ping\/pong\/$"

  Scenario: Named parameters
    Given The string '/:type/:id/'
    Then I should get:
      "^\/([\w]+)\/([\w]+)\/$"

  Scenario: Array to regEx
    Given The array ['foo', 'bar']
    Then I should get:
      "^\/foo\/bar\/$"

