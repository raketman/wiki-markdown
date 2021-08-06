function findFirstFile(childs) {
  for (var child in childs) {
    var item = childs[child];

    if (item.type === 'file') {
      return item;
    }

    item = findFirstFile(item.childs);
    if (item) {
      return item;
    }
  }
  return null;
}

function isFile(item)
{
    return item.type === 'file';
}

function isMarkdown(item)
{
  return item.options && (item.options.extension === 'md' || item.options.extension === 'markdown');
}

function findWikiBy(wiki, key, value)
{
  if (wiki[key] === value) {
    return wiki;
  }

  for(var child in wiki.childs) {
    var result = findWikiBy(wiki.childs[child], key, value);

    if (result) {
      return result;
    }
  }

  return null
}

function buildMenu(wiki)
{
  var item = {
    title: wiki.name,
    hiddenOnCollapse: true
  }


  if (isFile(wiki)) {
    item.href = wiki.id;

    if (wiki.options.links.length > 0) {
      item.child = [];

      for (var linkKey in wiki.options.links) {
        var link = wiki.options.links[linkKey];
        item.child.push({
          href: wiki.id +'#' + link,
          title: ' - ' + link,
          hiddenOnCollapse: true
        })
      }
    }
  } else {
    item.child = [];

    for(var child in wiki.childs) {
      item.child.push(buildMenu(wiki.childs[child]))
    }
  }

  return item;
}

function makeMap(wiki)
{

}

export default {
  findFirstFile: findFirstFile,
  isFile: isFile,
  makeMap: makeMap,
  findWikiBy: findWikiBy,
  buildMenu: buildMenu,
  isMarkdown: isMarkdown,
}
