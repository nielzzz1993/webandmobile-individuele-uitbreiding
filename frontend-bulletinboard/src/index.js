import React from "react";
import { render } from "react-dom";
import { Provider } from "react-redux";

import App from "./App/App";

import { createStore, compose, applyMiddleware } from "redux";

import thunk from "redux-thunk";
import reducers from "./reducers";
import { fetchPosts } from "./actions";

const store = createStore(reducers, {}, compose(applyMiddleware(thunk)));

store.dispatch(fetchPosts("messages"));

render(
  <Provider store={store}>
    <App />
  </Provider>,
  document.getElementById("root")
);

export default store;
