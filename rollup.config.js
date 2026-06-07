import { nodeResolve } from "@rollup/plugin-node-resolve";
import terser from "@rollup/plugin-terser";

export default {
	input: "js/edit_code.js",
	output: {
		file: "js/edit_code.bundle.js",
		format: "iife"
	},
	plugins: [
	    nodeResolve(),
        terser({ maxWorkers: 8 })
    ],
	treeshake: false
};