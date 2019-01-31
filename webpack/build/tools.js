const { dirname, resolve, extname } = require('path')
const glob = require('glob')
const fs = require('fs')
const deepMerge = require('deepmerge');
const { parse } = require('yamljs');
const root = process.cwd();
const _ = require('lodash')

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

function getRequiredModules(bundlePaths) {
    const requireContents = bundlePaths.map(path => {
        try {
            return parse(getFileContents(path))
        } catch(e) {
            return {}
        }
    })

    const requires = deepMerge.all(requireContents)
    const config = requires.config.config
    const aliases = _.mapValues(requires.config.paths, path => {
        return resolve(root, 'web/bundles', path)
    })

    return { aliases, config }
}

function getBundleData(bundlePaths) {
    const directories = bundlePaths.map(path => dirname(path))
    const extensions = getFormExtensions(directories)
    const requiredModules = getRequiredModules(bundlePaths)

    writeExtensions(extensions)

    return {
        extensions: extensions.extensions,
        attribute_fields: extensions.attribute_fields,
        aliases: requiredModules.aliases,
        config: requiresModules.config,
        styles: {}
    }
}

module.exports = getBundleData
