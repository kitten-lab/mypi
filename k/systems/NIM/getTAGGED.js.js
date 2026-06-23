let TAGS = {
  a: [],
  b: [],
  c: []
};

// 🔹 Load all three files
$.when(
  $.getJSON('/DEWEY/catalogs/a-node.tag.catalog.json'),
  $.getJSON('/DEWEY/catalogs/b-node.tag.catalog.json'),
  $.getJSON('/DEWEY/catalogs/c-node.tag.catalog.json')
).done(function(aData, bData, cData) {

  TAGS.a = Object.keys(aData[0]["a-node"] || {});
  TAGS.b = Object.keys(bData[0]["b-node"] || {});
  TAGS.c = Object.keys(cData[0]["c-node"] || {});

  initAutocomplete();
});

function getCurrentChain(value) {
  let parts = value.split(';');
  return parts[parts.length - 1]; // only the active chain
}

function getContext(value) {
  let chain = getCurrentChain(value);

  let ops = [];

  let iStar = chain.lastIndexOf('*');
  if (iStar !== -1) ops.push({ type: 'b', index: iStar });

  let iArrow = chain.lastIndexOf('>');
  if (iArrow !== -1) ops.push({ type: 'c', index: iArrow });

  let iAmp = chain.lastIndexOf('&');
  if (iAmp !== -1) ops.push({ type: 'b', index: iAmp });

  if (ops.length === 0) return 'a';

  // sort by most recent
  ops.sort((a, b) => b.index - a.index);

  return ops[0].type;
}

function getActiveToken(value) {
  let chain = getCurrentChain(value);
  let split = chain.split(/[\*\>,&]/);
  return split[split.length - 1];
}

function replaceActiveToken(fullValue, newToken) {
  let chains = fullValue.split(';');
  let current = chains.pop();

  let parts = current.split(/([\*\>,&])/);
  parts[parts.length - 1] = newToken;

  chains.push(parts.join(''));
  return chains.join(';');
}
// 🔹 Initialize autocomplete
function initAutocomplete() {

  $("#tag-input").autocomplete({
    minLength: 0,

    source: function(request, response) {
      let value = $("#tag-input").val();
      let context = getContext(value);
      let token = getActiveToken(value).toLowerCase();


      let pool = TAGS[context] || [];

      let matches = pool.filter(item =>
        item.toLowerCase().includes(token)
      );

      // optional: prioritize startsWith
      matches.sort((a, b) => {
        return a.startsWith(token) ? -1 : 1;
      });

      response(matches);
    },

    focus: function(event,ui) {
        event.preventDefault();
        return false;
    },

    select: function(event, ui) {
        let $input = $("#tag-input");
        let current = $input.val();

        let updated = replaceActiveToken(current, ui.item.value);
        

        if (getContext(current) === 'a'){
        updated += '*';
        };
        if (getContext(current) === 'b'){
        updated += '>';
        };

        if (getContext(current) === 'c'){
        updated += ',';
        };

        $input.val(updated);
        console.log("BEFORE:", current);
        console.log("SELECTED:", ui.item.value);
        console.log("AFTER:", updated);
      return false;
    }
  });

  // 🔹 Trigger suggestions live
  $("#tag-input").on("input", function() {
    $(this).autocomplete("search", getActiveToken(this.value));
  });
}