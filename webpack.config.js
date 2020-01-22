const path = require("path");
const webpack = require("webpack");
const VueLoaderPlugin = require("vue-loader/lib/plugin");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const Dotenv = require("dotenv-webpack");

module.exports = {
    entry: {
        documentBuilder: "./src/entries/documentBuilder/entry.js",
        schedule: "./src/entries/schedule/entry.js"
    },
    output: {
        path: path.resolve(__dirname, "dist/"),
        publicPath: "../",
        filename: "[name]/[name].js"
    },
    resolve: {
        modules: ["../../assets/node_modules/", "node_modules/"],
        alias: {
            GlobalAssets: path.resolve(__dirname, "../../assets/")
        }
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader"
                }
            },
            {
                test: /\.css$/,
                use: ["style-loader", "css-loader"]
            },
            {
                test: /\.(png|jpe?g|gif|svg|woff(2)?|ttf|eot)$/i,
                use: [
                    {
                        loader: "file-loader",
                        options: {
                            name: "[name].[ext]",
                            outputPath: "assets/"
                        }
                    }
                ]
            },
            {
                test: /\.vue$/,
                loader: "vue-loader"
            }
        ]
    },
    plugins: [
        new Dotenv({
            path: ".env"
        }),
        new VueLoaderPlugin(),
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery"
        }),
        new HtmlWebpackPlugin({
            template: "./src/entries/documentBuilder/template.html",
            filename: "documentBuilder/index.html",
            chunks: ["documentBuilder"],
            hash: true
        }),
        new HtmlWebpackPlugin({
            template: "./src/entries/schedule/template.html",
            filename: "schedule/index.html",
            chunks: ["schedule"],
            hash: true
        })
    ]
};
