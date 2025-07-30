// Webpack Watch Options for reducing file descriptors
// This file helps prevent EMFILE errors on macOS

module.exports = {
  watchOptions: {
    ignored: /node_modules/,
    aggregateTimeout: 300,
    poll: 1000,
  },
  resolve: {
    symlinks: false
  }
};
