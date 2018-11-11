//React
import React, { Component, Fragment } from "react";
//MaterialUI components
import AccountCircle from "@material-ui/icons/AccountCircle";
import IconButton from "@material-ui/core/IconButton";
import Menu from "@material-ui/core/Menu";
import MenuItem from "@material-ui/core/MenuItem";
import { Link } from "react-router-dom";
import { setLoggedIn } from "../LoginCookie";

export default class AccountsMenu extends Component {
  state = {
    anchorEl: null
  };

  handleClick = event => {
    this.setState({ anchorEl: event.currentTarget });
  };

  handleLogout() {
    setLoggedIn("false");
    window.location.reload();
  }

  handleClose = () => {
    this.setState({ anchorEl: null });
  };

  render() {
    const { anchorEl } = this.state;

    return (
      <Fragment>
        <IconButton
          aria-owns={anchorEl ? "accounts-menu" : null}
          aria-haspopup="true"
          onClick={this.handleClick}
        >
          <AccountCircle color="secondary" />
        </IconButton>
        <Menu
          anchorEl={anchorEl}
          open={Boolean(anchorEl)}
          onClose={this.handleClose}
        >
          <MenuItem>
            <Link style={{ color: "#FFF", textDecoration: "none" }} to="/login">
              Login
            </Link>
          </MenuItem>
          <MenuItem onClick={this.handleLogout}>Logout</MenuItem>
        </Menu>
      </Fragment>
    );
  }
}
