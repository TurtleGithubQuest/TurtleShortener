import globals from "globals";
import typescriptEslint from "@typescript-eslint/eslint-plugin";
import tsParser from "@typescript-eslint/parser";
import path from "node:path";
import { fileURLToPath } from "node:url";
import js from "@eslint/js";
import { FlatCompat } from "@eslint/eslintrc";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all
});

export default [{
    languageOptions: {
        globals: {
            ...globals.node,
            ...globals.jquery,
            ...globals.browser,
        },

        ecmaVersion: "latest",
        sourceType: "module",
    },

    rules: {
        indent: ["error", "tab", {
            SwitchCase: 1,
        }],

        "linebreak-style": ["error", "unix"],
        semi: ["error", "always"],
    },
}, ...compat.extends("eslint:recommended").map(config => ({
    ...config,
    files: ["**/*.js"],
})), {
    files: ["**/*.js"],

    languageOptions: {
        globals: {
            _: "readonly",
            Espo: "readonly",
            Bull: "readonly",
            define: "readonly",
            extend: "readonly",
            Backbone: "readonly",
            GridStack: "readonly",
        },
    },

    rules: {
        "no-unused-vars": ["error", {
            vars: "all",
            args: "all",
            argsIgnorePattern: "^_",
        }],
    },
}, ...compat.extends("eslint:recommended", "plugin:@typescript-eslint/recommended").map(config => ({
    ...config,
    files: ["**/*.ts"],
})), {
    files: ["**/*.ts"],

    plugins: {
        "@typescript-eslint": typescriptEslint,
    },

    languageOptions: {
        parser: tsParser,
    },

    rules: {
        "no-unused-vars": "off",
        "@typescript-eslint/no-explicit-any": "off",

        "@typescript-eslint/ban-ts-comment": ["error", {
            "ts-ignore": "allow-with-description",
        }],
    },
}];