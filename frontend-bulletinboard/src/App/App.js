//React
import React from "react";
import { BrowserRouter, Route } from "react-router-dom";
import { MuiThemeProvider } from "@material-ui/core/styles";

import HomePage from "../HomePage/HomePage";
import ReactionsPage from "../ReactionsPage/ReactionsPage";
import LoginPage from "../LoginPage/LoginPage";
import PXL_THEME from "./PxlReactTheme";
import "./App.css";

export default App => (
  <BrowserRouter>
    <MuiThemeProvider theme={PXL_THEME}>
      <Route exact path="/" component={HomePage} />
      <Route path="/reactions" component={ReactionsPage} />
      <Route path="/login" component={LoginPage} />
    </MuiThemeProvider>
  </BrowserRouter>
);
