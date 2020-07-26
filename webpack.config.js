const path = require("path");
const webpack = require("webpack");
const VueLoaderPlugin = require("vue-loader/lib/plugin");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const Dotenv = require("dotenv-webpack");

module.exports = env => {
    return {
        entry: {
            documentBuilder: "./src/entries/documentBuilder/entry.js",
            schedule: "./src/entries/schedule/entry.js",
            newActionSchedule: "./src/entries/schedule/newActionEntry.js",
            qr: "./src/entries/qr/entry.js"
        },
        output: {
            path: path.resolve(__dirname, "dist/"),
            publicPath: "../",
            filename: "[name]/[name].js"
        },
        resolve: {
            modules: ["../../node_modules/", "node_modules/"],
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
                path: env.NODE_ENV == "local" ? ".env" : ".env.production"
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
            }),
            new HtmlWebpackPlugin({
                template: "./src/entries/schedule/template.html",
                filename: "newActionSchedule/index.html",
                chunks: ["newActionSchedule"],
                hash: true
            }),
            new HtmlWebpackPlugin({
                template: "./src/entries/qr/template.html",
                filename: "qr/index.html",
                chunks: ["qr"],
                hash: true
            })
        ]
    };
};
