document.addEventListener('DOMContentLoaded', function () {
  var ta = document.getElementById('editor-body');
  if (!ta) return;

  var actions = {
    bold:       { wrap: ['**', '**'], placeholder: 'bold text' },
    italic:     { wrap: ['*',  '*' ], placeholder: 'italic text' },
    h2:         { line: '## ',  placeholder: 'Heading' },
    h3:         { line: '### ', placeholder: 'Sub-heading' },
    link:       { template: function(sel){ return '[[' + (sel || 'Page Name') + ']]'; } },
    blockquote: { line: '> ',   placeholder: 'Quote text' },
    ul:         { line: '- ',   placeholder: 'List item' },
    statblock:  { template: function() {
      return '::: statblock\nname: \ncr: 1\nxp: 400\ntype: N Medium humanoid\ninit: +0\nsenses: Perception +0\n\n# Defense\nAC: 10, touch 10, flat-footed 10\nHP: 10 (2d8)\nSaves: Fort +0, Ref +0, Will +0\n\n# Offense\nSpeed: 30 ft.\nMelee: shortswword +1 (1d6)\n\n# Statistics\nStr: 10\nDex: 10\nCon: 10\nInt: 10\nWis: 10\nCha: 10\nBAB: +0\nCMB: +0\nCMD: 10\nFeats: \nSkills: \nLanguages: Common\nGear: \n\n# Special Abilities\n:::';
    }},
    map:        { template: function() {
      return '::: map src=/uploads/map.png caption="Map caption" scale="1 league"\npin x=50 y=50 label="Location" cap=1\n:::';
    }},
    gm:         { template: function() {
      return '::: gm GM Eyes Only\nGM-only content here.\n:::';
    }},
  };

  function insertAtCursor(before, after, placeholder) {
    var start = ta.selectionStart, end = ta.selectionEnd;
    var sel   = ta.value.substring(start, end) || placeholder;
    var text  = before + sel + after;
    ta.setRangeText(text, start, end, 'select');
    ta.focus();
  }

  function insertLine(prefix, placeholder) {
    var start = ta.selectionStart;
    var lineStart = ta.value.lastIndexOf('\n', start - 1) + 1;
    var lineEnd   = ta.value.indexOf('\n', start);
    if (lineEnd === -1) lineEnd = ta.value.length;
    var line = ta.value.substring(lineStart, lineEnd) || placeholder;
    ta.setRangeText(prefix + line, lineStart, lineEnd, 'end');
    ta.focus();
  }

  document.getElementById('editor-toolbar').addEventListener('click', function (e) {
    var btn = e.target.closest('[data-action]');
    if (!btn) return;
    e.preventDefault();
    var key = btn.dataset.action;
    var a   = actions[key];
    if (!a) return;

    if (a.template) {
      var text = a.template(ta.value.substring(ta.selectionStart, ta.selectionEnd));
      ta.setRangeText('\n' + text + '\n', ta.selectionStart, ta.selectionEnd, 'end');
      ta.focus();
    } else if (a.wrap) {
      insertAtCursor(a.wrap[0], a.wrap[1], a.placeholder);
    } else if (a.line) {
      insertLine(a.line, a.placeholder);
    }

    // Trigger HTMX preview refresh
    ta.dispatchEvent(new Event('keyup', { bubbles: true }));
  });

  // Image upload
  var imgInput = document.getElementById('img-upload');
  if (imgInput) {
    imgInput.addEventListener('change', function () {
      var file = imgInput.files[0];
      if (!file) return;
      var fd = new FormData();
      fd.append('file', file);
      fetch('/upload', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(data) {
          if (data.url) {
            var md = '![' + file.name + '](' + data.url + ')';
            ta.setRangeText(md, ta.selectionStart, ta.selectionEnd, 'end');
            ta.dispatchEvent(new Event('keyup', { bubbles: true }));
          }
        });
      imgInput.value = '';
    });
  }

  // Keyboard shortcut / to focus search
  document.addEventListener('keydown', function(e) {
    if (e.key === '/' && document.activeElement !== ta) {
      var s = document.querySelector('input[name="q"]');
      if (s) { e.preventDefault(); s.focus(); }
    }
  });
});
