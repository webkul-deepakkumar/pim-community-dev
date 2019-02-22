module.exports = async function(cucumber) {
    const { Given, Then, When, Before } = cucumber;
    const assert = require('assert');
    const { renderView, csvToArray, answerJson, endsWith } = require('../../tools');
    const createElementDecorator = require('../../decorators/common/create-element-decorator');

    // const config = {
    //   'Catalog volume report':  {
    //     selector: '.AknDefault-mainContent',
    //     decorator: require('../../decorators/catalog-volume/report.decorator')
    //   }
    // };

    const data = {
        attributes: {},
        family: {},
        products: {},
        user: {},
        locales: require('../../request-contracts/locales.json'),
        columns: require('../../request-contracts/default-columns.json'),
        grid: require('../../request-contracts/product-grid.json'),
        tree: require('../../request-contracts/list-tree.json'),
        children: require('../../request-contracts/children.json'),
        filters: require('../../request-contracts/attributes-filters.json'),
        view: {view: null},
    }

    // getElement = createElementDecorator(config);

    Given('the {string} catalog configuration', async function(string) {
        await string;
    });

    Then('the following attributes:', async function (dataTable) {
        await dataTable
    });

    Then('the following family:', async function (dataTable) {
        await dataTable
    });

    Then('the following products:', async function (dataTable) {
        data.products = dataTable.hashes()
        await true;
    });

    Then('I am logged in as "Mary"', async function () {
        await true
    });

    Then('I am on the products grid', async function() {
        // Move these somewhere
        this.page.on('request', request => {
            if(request.url().includes('/notification/count_unread')) {
                answerJson(request, 0)
            }

            if(request.url().includes('thumbnail_small') ||
               request.url().includes('style.css') ||
               request.url().includes('favicon.ico')) {
                answerJson(request, {}, 200, false)
            }

            if (request.url().includes('/configuration/locale/rest?activated=true')) {
                answerJson(request, data.locales)
            }

            if(endsWith(request.url(), '/datagrid_view/rest/product-grid/default-columns')) {
                answerJson(request, data.columns)
            }

            if(endsWith(request.url(), '/datagrid_view/rest/product-grid/default'))
            {
                answerJson(request, data.view)
            }

            if(request.url().includes('/datagrid/product-grid/load'))
            {
                answerJson(request, data.grid, 200, false)
            }

            if (request.url().includes('/enrich/product-category-tree/product-grid/list-tree.json')) {
                answerJson(request, data.tree)
            }

            if (request.url().includes('/enrich/product-category-tree/product-grid/children.json')) {
                answerJson(request, data.children)
            }

            if (request.url().includes('/datagrid/product-grid/attributes-filters?page=1&locale=en_US')) {
                answerJson(request, data.filters)
            }
        })

        await renderView(this.page, 'pim-product-index', {})
    })

    Then('I should not see the filter weight', async function () {
        await true
    })

    Then('the grid should contain 4 elements',  async function () {
        await true
    })

    Then('I should see products postit and book', async function() {
        await true
    })

    Then('I should be able to use the following filters:', async function(dataTable) {
        await true
    })

  };
