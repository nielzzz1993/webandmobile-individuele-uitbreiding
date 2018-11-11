import React, { Component, Fragment } from "react";
import Button from "@material-ui/core/Button";
import Dialog from "@material-ui/core/Dialog";
import DialogActions from "@material-ui/core/DialogActions";
import DialogContent from "@material-ui/core/DialogContent";
import DialogTitle from "@material-ui/core/DialogTitle";
//MaterialUI Icons
import AddIcon from "@material-ui/icons/Add";

const styles = {
  fabButton: {
    position: "fixed",
    bottom: "20pt",
    right: "28pt",
    width: "35pt",
    height: "35pt"
  }
};

export default class FormDialog extends Component {
  dialog = {
    title: "Title",
    textboxes: null
  };

  state = {
    open: false
  };

  handleClickOpen = () => {
    this.setState({ open: true });
  };

  handleClose = () => {
    this.setState({ open: false });
  };

  render() {
    return (
      <Fragment>
        <Dialog
          open={this.state.open}
          onClose={this.handleClose}
          aria-labelledby="form-dialog-title"
        >
          <DialogTitle>{this.dialog.title}</DialogTitle>
          <DialogContent>{this.dialog.textboxes}</DialogContent>
          <DialogActions>
            <Button onClick={this.handleClose} color="primary">
              Annuleren
            </Button>
            <Button onClick={this.handlePost} color="primary">
              Posten
            </Button>
          </DialogActions>
        </Dialog>
        <Button
          color="primary"
          onClick={this.handleClickOpen}
          style={styles.fabButton}
          variant="fab"
        >
          <AddIcon />
        </Button>
      </Fragment>
    );
  }
}
