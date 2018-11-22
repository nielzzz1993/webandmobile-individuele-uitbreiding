//React
import React, { Component } from "react";
//MaterialUI components
import AccountsMenu from "./AccountsMenu";
import AppBar from "@material-ui/core/AppBar";
import SearchMenu from "./SearchMenu";
import Toolbar from "@material-ui/core/Toolbar";
import Typography from "@material-ui/core/Typography";
import Button from '@material-ui/core/Button';
//Images
import logoPXL from "../../images/logoPXL.png";
import { getLoggedIn } from "../LoginCookie";

export default class NavBar extends Component {
  render() {
    return (
      <AppBar>
        <Toolbar color="primary">
          <a href="http://localhost:3000#top">
            <img
              src={logoPXL}
              style={{ width: "38pt", padding: "2pt" }}
              alt="PXL logo"
              title="PXL logo"
            />
          </a>
          <Typography color="secondary" variant="h1" style={{ padding: "5pt" }}>
            Forum
          </Typography>
          {getLoggedIn === "true" &&
            <Button color="inherit">Profile</Button>
          }
          <div style={{ flexGrow: 1 }} />
          <SearchMenu style={{ float: "right" }} />
          <AccountsMenu style={{ float: "right" }} />
        </Toolbar>
      </AppBar>
    );
  }
}
