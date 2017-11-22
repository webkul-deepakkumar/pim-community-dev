@javascript
Feature: Edit common attributes of many products and product models at once
  In order to update many products with the same information
  As a product manager
  I need to be able to edit attributes of many products and product models at once

  Background:
    Given the "catalog_modeling" catalog configuration
    And the following root product models:
      | code      | family_variant      | description-en_US-ecommerce      |
      | model-col | clothing_color_size | Magnificent Cult of Luna t-shirt |
      | model-nin | clothing_size       |                                  |
    And the following sub product models:
      | code            | parent    | family_variant      | color | composition             |
      | model-col-white | model-col | clothing_color_size | white | cotton 90%, viscose 10% |
      | model-col-blue  | model-col | clothing_color_size | blue  | cotton 70%, viscose 30% |
    And the following products:
      | sku         | family   | parent          | color | size | description-en_US-ecommerce | composition | weight   |
      | col-white-m | clothing | model-col-white |       | m    |                             |             | 478 GRAM |
      | col-white-s | clothing | model-col-white |       | s    |                             |             | 478 GRAM |
      | nin-s       | clothing | model-nin       | black | s    |                             | 100% cotton |          |
      | tool-tee    | clothing |                 | black | m    | Tool tee                    |             |          |
    And I am logged in as "Julia"

  Scenario: Mass edit attributes of a product model inside a family variant with 2 levels of hierarchy
    Given I am on the products grid
    And I select rows model-col
    And I press the "Bulk actions" button
    And I choose the "Edit common attributes" operation
    And I display the Model description attribute
    And I change the "Model description" to "Just a t-shirt"
    And I confirm mass edit
    And I wait for the "edit_common_attributes" job to finish
    Then the english localizable value name of "model-col" should be "boots"
    And the english localizable value name of "model-col-white" should be "boots"
    And the english localizable value name of "model-col-blue" should be "boots"
    And the english localizable value name of "col-white-m" should be "boots"
    And the english localizable value name of "col-white-s" should be "boots"

  #Scenario: Mass edit attributes of a sub product model inside a family variant with 2 levels of hierarchy
  #Scenario: Mass edit attributes of a product model inside a family variant with 1 levels of hierarchy
  #Scenario: Mass edit attributes of a root product model, a sub product model and a non variant product at the same time
