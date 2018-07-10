/* global window, document */
if (! window._babelPolyfill) {
  require('babel-polyfill');
}

import React from 'react';
import ReactDOM from 'react-dom';
import Widget from './containers/Widget';

document.addEventListener('DOMContentLoaded', function() {
  const widget_containers = document.querySelectorAll('.wp-live-stats-widget');

  for (let i = 0; i < widget_containers.length; ++i) {
    const objectId = widget_containers[i].getAttribute('data-object-id');

    ReactDOM.render(<Widget wpObject={window[objectId]} />, widget_containers[i]);
  }
});
