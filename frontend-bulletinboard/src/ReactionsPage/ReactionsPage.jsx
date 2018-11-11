//React
import React, { Component, Fragment } from "react";
//Components
import LinkButton from "../components/material-ui/LinkButton";
import NavBar from "../components/material-ui/NavBar";
import Footer from "../components/material-ui/Footer";
import ReactionList from "../components/material-ui/ReactionList";
import AddReactionDialog from "../components/material-ui/AddReactionDialog";
//MaterialUI Icons
import { connect } from "react-redux";
import { fetchPosts } from "../actions";
import ProgressBar from "../components/materialDesignLite/ProgressBar";

const styles = {
  main: {
    margin: "75pt 25pt",
    padding: "0 auto",
    fontSize: "15pt"
  }
};

const mapDispatchToProps = {
  getPosts: fetchPosts
};

const mapStateToProps = state => ({
  loading: state.reaction.loading
});

class ReactionsPage extends Component {
  render() {
    let { getPosts, loading } = this.props;
    return (
      <Fragment>
        <NavBar />
        <main style={styles.main}>
          {loading === false ? <ReactionList /> : <ProgressBar />}
          <LinkButton
            to="/"
            color="primary"
            variant="contained"
            style={{ margin: "10pt 0" }}
            onClick={() => {
              getPosts("messages");
            }}
          >
            Back to posts
          </LinkButton>
          <AddReactionDialog />
        </main>
        <Footer />
      </Fragment>
    );
  }
}

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(ReactionsPage);
