import React from "react";
import TextField from "@material-ui/core/TextField";
import FormDialog from "./FormDialog";
import CategorySelector from "./CategorySelector";
import { addPost } from "../../actions";

export default class AddPostDialog extends FormDialog {
  constructor(props) {
    super(props);
    this.state = { title: "", content: "", category: "None" };
    this.handlePost = this.handlePost.bind(this);
  }

  handleCategory = categoryValue => {
    this.setState({ category: categoryValue });
  };

  dialog = {
    title: "Maak post",
    textboxes: [
      [<CategorySelector onSelectCategory={this.handleCategory} />],
      [
        <TextField
          placeholder="Titel"
          variant="outlined"
          value={this.state.title}
          onChange={this.handleChange.bind(this)}
          style={{ width: "100%" }}
        />
      ],
      [<br />],
      [
        <TextField
          placeholder="Mededeling"
          multiline
          rows={3}
          rowsMax="5"
          variant="outlined"
          value={this.state.content}
          onChange={this.handleChange.bind(this)}
          style={{ width: "100%" }}
        />
      ]
    ]
  };

  handleChange(event) {
    this.setState({ title: event.target.value });
    this.setState({ content: event.target.value });
  }

  handlePost() {
    addPost(1, this.state.title, this.state.content, this.state.category);
    this.handleClose();
  }
}
