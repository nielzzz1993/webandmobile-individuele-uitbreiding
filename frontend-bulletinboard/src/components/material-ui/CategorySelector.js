//React
import React, { Component, Fragment } from "react";
//Components
import List from "@material-ui/core/List";
import ListItem from "@material-ui/core/ListItem";
import ListItemText from "@material-ui/core/ListItemText";
import MenuItem from "@material-ui/core/MenuItem";
import Menu from "@material-ui/core/Menu";
//MaterialUI Icons
import ArrowDropDownIcon from "@material-ui/icons/ArrowDropDown";

const options = [
  "None",
  "PXL meldingen",
  "PXL belangrijke meldingen",
  "PXL memes"
];

export default class CategorySelector extends Component {
  constructor(props) {
    super(props);
    this.handleClickListItem = this.handleClickListItem.bind(this);
    this.handleMenuItemClick = this.handleMenuItemClick.bind(this);
    this.handleClose = this.handleClose.bind(this);
  }

  state = {
    anchorEl: null,
    selectedIndex: 0
  };

  handleClickListItem = event => {
    this.setState({ anchorEl: event.currentTarget });
  };

  handleMenuItemClick = (event, index) => {
    this.props.onSelectCategory(options[index]);
    this.setState({ selectedIndex: index, anchorEl: null });
  };

  handleClose = () => {
    this.setState({ anchorEl: null });
  };

  render() {
    const { anchorEl } = this.state;

    return (
      <Fragment>
        <List style={{ padding: 0 }}>
          <ListItem
            button
            aria-haspopup="true"
            onClick={this.handleClickListItem}
          >
            <ListItemText primary={options[this.state.selectedIndex]} />
            <ArrowDropDownIcon color="secondary" />
          </ListItem>
        </List>
        <Menu
          anchorEl={anchorEl}
          open={Boolean(anchorEl)}
          onClose={this.handleClose}
        >
          {options.map((option, index) => (
            <MenuItem
              key={option}
              selected={index === this.state.selectedIndex}
              onClick={event => this.handleMenuItemClick(event, index)}
            >
              {option}
            </MenuItem>
          ))}
        </Menu>
      </Fragment>
    );
  }
}
