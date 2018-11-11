//React
import React, { Component, Fragment } from "react";
//Components
import CategorySelector from "./CategorySelector";
import IconButton from "@material-ui/core/IconButton";
import TextField from "@material-ui/core/TextField";
import Menu from "@material-ui/core/Menu";
import MenuItem from "@material-ui/core/MenuItem";
//MaterialUI Icons
import SearchIcon from "@material-ui/icons/Search";
import { searchPosts, searchPostsCategory } from "../../actions";
import { connect } from "react-redux";

const mapDispatchToProps = {
  findPosts: searchPosts,
  findPostsCategory: searchPostsCategory
};

class SearchMenu extends Component {
  state = {
    anchorEl: null,
    keywords: "",
    category: "None"
  };

  handleChange = event => {
    this.setState({ keywords: event.target.value });
  };

  handleClick = event => {
    this.setState({ anchorEl: event.currentTarget });
  };

  handleClose = () => {
    this.setState({ anchorEl: null });
  };

  handleCategory = categoryValue => {
    this.setState({ category: categoryValue });
  };

  handleSearch() {
    if (this.state.category === "None") {
      this.props.findPosts(this.state.keywords);
    } else {
      this.props.findPostsCategory(this.state.keywords, this.state.category);
    }
  }

  render() {
    const { anchorEl } = this.state;

    return (
      <Fragment>
        <IconButton onClick={this.handleClick}>
          <SearchIcon color="secondary" />
        </IconButton>
        <Menu
          anchorEl={anchorEl}
          open={Boolean(anchorEl)}
          onClose={this.handleClose}
        >
          <MenuItem>
            <CategorySelector onSelectCategory={this.handleCategory} />
          </MenuItem>
          <MenuItem>
            <TextField
              hidden={Boolean(!anchorEl)}
              placeholder="Zoek..."
              className="fade"
              variant="outlined"
              value={this.state.keywords}
              onChange={this.handleChange}
              onKeyPress={ev => {
                if (ev.key === "Enter") {
                  this.handleSearch();
                }
              }}
            />
            <IconButton
              onClick={() => {
                this.handleSearch();
              }}
            >
              <SearchIcon color="secondary" />
            </IconButton>
          </MenuItem>
        </Menu>
      </Fragment>
    );
  }
}

export default connect(
  null,
  mapDispatchToProps
)(SearchMenu);
