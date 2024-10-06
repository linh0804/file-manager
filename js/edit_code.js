import { EditorView } from "codemirror"
import { EditorState, Compartment } from "@codemirror/state"

import {
    keymap,
    lineNumbers,
    highlightActiveLine,
    highlightSpecialChars,
    highlightActiveLineGutter,
    drawSelection,
    dropCursor
} from "@codemirror/view"

import { history, historyKeymap } from "@codemirror/commands"

import {
    bracketMatching,
    foldGutter,
    indentService
} from "@codemirror/language"

import { highlightSelectionMatches } from "@codemirror/search"

// theme
import { materialDark } from "cm6-theme-material-dark"

// lang
import { css } from "@codemirror/lang-css"
import { html } from "@codemirror/lang-html"
import { javascript } from "@codemirror/lang-javascript"
import { json } from "@codemirror/lang-json"
import { php } from "@codemirror/lang-php"
import { sql } from "@codemirror/lang-sql"

const languageConf = new Compartment()
const lineWrapConf = new Compartment()

const editor = new EditorView({
    state: EditorState.create({
        doc: document.querySelector("#content").value,

        extensions: [
            EditorState.allowMultipleSelections.of(false),

            bracketMatching(),
            highlightSelectionMatches(),

            history(),
            drawSelection(),
            dropCursor(),

            foldGutter(),

            lineNumbers(),
            highlightActiveLineGutter(),
            highlightActiveLine(),
            highlightSpecialChars(),

            indentService.of(undefined),
            keymap.of([
                /*
                {
                    key: "Tab",
                    preventDefault: true,
                    run: ({state, dispatch}) => {
                        dispatch(state.update(
                            state.replaceSelection("    "),
                            { scrollIntoView: true, userEvent: "input" }
                        ))

                        return true
                    }
                },
                */
                ...historyKeymap
            ]),

            lineWrapConf.of([]),
            languageConf.of([]),

            materialDark
        ]    
    }),
    parent: document.querySelector("#editor")
});

// doi ngon ngu
var codeLangElement = document.getElementById("code_lang");
codeLangElement.addEventListener("change", function () {
    var mode = codeLangElement.value;

    editor.dispatch({
        effects: languageConf.reconfigure(
            getLang(mode)
        )
    })
});

// ngon ngu mac dinh
editor.dispatch({
    effects: languageConf.reconfigure(
        getLang(codeLangElement.value)
    )
})

function getLang(mode) {
    let lang = [];

    switch (mode) {
        case 'html': lang = html(); break;
        case 'css': lang = css(); break;

        case 'javascript': lang = javascript(); break;
        case 'json': lang = json(); break;

        case 'php': lang = php(); break;
        case 'sql': lang = sql(); break;
    }

    return lang;
}

// che do wrap
var codeWrapElement = document.getElementById("code_wrap");
codeWrapElement.addEventListener("change", function () {
    let wrap = [];

    if (codeWrapElement.checked) {
        wrap = EditorView.lineWrapping;
    }

    editor.dispatch({
        effects: lineWrapConf.reconfigure(wrap)
    })
});

// xuat bien toan cau
window.editor = editor;
