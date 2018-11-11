import React from "react";
import TextField from "@material-ui/core/TextField";
import FormDialog from "./FormDialog";
import { addReaction } from "../../actions";
import { connect } from "react-redux";

const mapDispatchToProps = {
  postReaction: addReaction
};
class AddReactionDialog extends FormDialog {
  constructor(props) {
    super(props);
    this.state = {
      content: ""
    };
    this.handlePost = this.handlePost.bind(this);
  }

  dialog = {
    title: "Maak reactie",
    textboxes: (
      <TextField
        placeholder="Reactie"
        multiline
        rowsMax="5"
        variant="outlined"
        value={this.state.content}
        onChange={this.handleChange.bind(this)}
      />
    )
  };

  handleChange(event) {
    this.setState({
      content: event.target.value
    });
  }

  handlePost() {
    var messageId = window.location.href.split("/").pop();
    this.props.postReaction(
      messageId,
      this.state.content
    );
    this.handleClose();
  }
}

export default connect(
  null,
  mapDispatchToProps
)(AddReactionDialog);
