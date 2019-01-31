const { dirname } = require('path')
const glob = require('glob')
const fs = require('fs')
const deepMerge = require('deepmerge');
const { parse } = require('yamljs');

function getFileContents(directory) {
    return fs.readFileSync(directory, 'utf-8');
}

function getFormExtensions(directories) {
    const filePaths = directories.map(dir => {
        return glob.sync(`${dir}/{form_extensions.yml,form_extensions/**/*.yml}`)
    });

    const files = filePaths.concat.apply([], filePaths)
    const contents = files.map(file => parse(getFileContents(file)))

    return deepMerge.all(contents)
}

function writeExtensions(extensions) {
    fs.writeFileSync('./web/js/extensions.json', JSON.stringify(extensions), 'utf-8')
}

function getBundleData(bundlePaths) {
    const directories = bundlePaths.map(path => dirname(path))
    const extensions = getFormExtensions(directories)

    writeExtensions(extensions)

    return {
        extensions: extensions.extensions,
        attribute_fields: extensions.attribute_fields,
        aliases: {},
        config: {},
        styles: {}
    }
}

module.exports = getBundleData
