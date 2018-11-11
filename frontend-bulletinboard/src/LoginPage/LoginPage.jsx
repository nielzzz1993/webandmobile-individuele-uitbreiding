//React
import React, { Fragment, Component } from "react";
//Components
import Button from "@material-ui/core/Button";
import TextField from "@material-ui/core/TextField";
import Typography from "@material-ui/core/Typography";
import Footer from "../components/material-ui/Footer";
//Images
import bigLogoPXL from "../images/bigLogoPXL.png";
import studentsImage from "../images/studentsPXL.png";
import { setLoggedIn } from "../components/LoginCookie";

const styles = {
  loginForm: {
    float: "right",
    width: "225pt",
    padding: "5pt",
    position: "absolute",
    top: 0,
    right: 25,
    height: "90vh"
  },
  studentsImage: {
    backgroundImage: `url(${studentsImage})`,
    backgroundRepeat: "no-repeat",
    backgroundSize: "cover",
    marginRight: "260pt",
    height: "90vh"
  }
};

class LoginPage extends Component {
  constructor(props) {
    super(props);
    this.state = {};
    this.onSubmit = this.onSubmit.bind(this);
  }

  render() {
    let { email, password } = this.state;

    return (
      <Fragment>
        <main>
          <div style={styles.studentsImage} />
          <div style={styles.loginForm}>
            <form name="loginForm" onSubmit={this.onSubmit}>
              <div className="form-group-collection">
                <div className="form-group">
                  <img src={bigLogoPXL} alt="PXL logo" />
                  <Typography variant="h5">Aanmelden</Typography>
                </div>
                <div className="form-group">
                  <TextField
                    type="email"
                    name="email"
                    onChange={e => this.setState({ email: e.target.value })}
                    label="iemand@example.com"
                    variant="outlined"
                    value={email}
                    style={{ width: "100%" }}
                  />
                </div>
                <div className="form-group">
                  <TextField
                    name="password"
                    onChange={e => this.setState({ password: e.target.value })}
                    label="Wachtwoord"
                    type="password"
                    variant="outlined"
                    value={password}
                    style={{ width: "100%" }}
                  />
                </div>
              </div>
              <Button type="submit" color="primary" variant="contained">
                Aanmelden
              </Button>
            </form>
          </div>
        </main>
        <Footer />
      </Fragment>
    );
  }
  onSubmit(e) {
    e.preventDefault();
    let { email, password } = this.state;
    if (email === "admin@gmail.com" && password === "password") {
      setLoggedIn("true");
      window.location.replace("/");
    }
    this.setState({
      email: "",
      password: ""
    });
  }
}

export default LoginPage;
