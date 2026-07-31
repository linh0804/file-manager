// core
import { EditorView, showPanel } from "@codemirror/view";
import { EditorState, Compartment } from "@codemirror/state";

// theme
import { oneDark } from "@codemirror/theme-one-dark";

// ext
import { keymap, highlightActiveLine } from "@codemirror/view";
import { history, historyKeymap } from "@codemirror/commands";
import { bracketMatching } from "@codemirror/language";
import { highlightSelectionMatches } from "@codemirror/search"

// lang
import { css } from "@codemirror/lang-css";
import { html } from "@codemirror/lang-html";
import { javascript } from "@codemirror/lang-javascript";
import { json } from "@codemirror/lang-json";
import { php } from "@codemirror/lang-php";
import { sql } from "@codemirror/lang-sql";
import { markdown } from "@codemirror/lang-markdown";
import { jinja } from "@codemirror/lang-jinja";
import { yaml } from "@codemirror/lang-yaml";

function curr_cursor_get(state) {
  let { head } = state.selection.main;
  let line = state.doc.lineAt(head);

  return `${line.number}:${head - line.from}`;
}

function curr_cursor_panel(view) {
  let dom = document.createElement("div")
  dom.textContent = '0:0'

  return {
    dom,
    update(update) {
      if (update.docChanged || update.selectionSet) {
        dom.textContent = curr_cursor_get(update.state);
      }
    }
  }
}

function get_text_lang(mode) {
    let lang = [];

    switch (mode) {
        case "html":
            lang = html();
            break;

        case "css":
            lang = css();
            break;

        case "javascript":
            lang = javascript();
            break;

        case "json":
            lang = json();
            break;

        case "php":
            lang = php();
            break;

        case "sql":
            lang = sql();
            break;

        case "markdown":
            lang = markdown();
            break;

        case "jinja":
            lang = jinja();
            break;

        case "yaml":
            lang = yaml();
            break;
    }

    return lang;
}

const languageConf = new Compartment();
const lineWrapConf = new Compartment();
const codeLangElement = document.getElementById("code_lang");

const editor = new EditorView({
    parent: document.querySelector("#editor"),
    state: EditorState.create({
        doc: document.querySelector("#editor-content").value,
        extensions: [
            // theme
            oneDark,
            showPanel.of(curr_cursor_panel),

            highlightActiveLine(),
            highlightSelectionMatches(),

            history(),
            bracketMatching(),
            EditorState.allowMultipleSelections.of(false),
            EditorState.tabSize.of(4),

            lineWrapConf.of([]),
            languageConf.of([]),

            keymap.of([
                ...historyKeymap,
                {
                    key: "Tab",
                    preventDefault: true,
                    run: ({state, dispatch}) => {
                        dispatch(state.update(
                            state.replaceSelection("    "),
                            { scrollIntoView: true, userEvent: "input" }
                        ))

                        return true;
                    }
                },
            ]),
        ],
    })
});

// ngon ngu mac dinh
editor.dispatch({
    effects: languageConf.reconfigure(get_text_lang(codeLangElement.value)),
});


// doi ngon ngu
codeLangElement.addEventListener("change", function () {
    editor.dispatch({
        effects: languageConf.reconfigure(get_text_lang(codeLangElement.value)),
    });
});


// che do wrap
window.editorSetWrap = function (enabled) {
    editor.dispatch({
        effects: lineWrapConf.reconfigure(
            enabled ? EditorView.lineWrapping : []
        ),
    });
};

// xuat bien toan cau
window.editor = editor;
