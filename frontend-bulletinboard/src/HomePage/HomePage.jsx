//React
import React, { Component, Fragment } from "react";
//Components
import NavBar from "../components/material-ui/NavBar";
import Footer from "../components/material-ui/Footer";
import AddPostDialog from "../components/material-ui/AddPostDialog";
import { connect } from "react-redux";
import MessageList from "../components/material-ui/MessageList";
import ProgressBar from "../components/materialDesignLite/ProgressBar";

const styles = {
  main: {
    margin: "75pt 25pt",
    padding: "0 auto",
    fontSize: "15pt"
  }
};

const mapStateToProps = state => ({
  loading: state.message.loading
});

class HomePage extends Component {
  render() {
    let { loading } = this.props;
    return (
      <Fragment>
        <NavBar />
        <main style={styles.main}>
          {loading === false ? <MessageList /> : <ProgressBar />}
          <AddPostDialog />
        </main>
        <Footer />
      </Fragment>
    );
  }
}

export default connect(mapStateToProps)(HomePage);
