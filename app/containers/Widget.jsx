import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from '../utils/fetchWP';

export default class Widget extends Component {

  constructor(props) {
    super(props);

    this.state = {
      stats: []
    };

    this.fetchWP = new fetchWP({
      restURL: this.props.wpObject.api_url,
      restNonce: this.props.wpObject.api_nonce,
    });

    this.getStats();
  }

  async componentDidMount() {
    try {
      setInterval(async () => {
        this.getStats();
      }, 60000);
    } catch(e) {
      console.log(e);
    }
  }

  getStats = () => {
    this.fetchWP.get( 'stats' )
    .then(
      (stats) => {
        let statsArray = Object.keys(stats).map(key => {
          return stats[key];
        });

        this.setState({
          stats: statsArray
        })
      },
      (err) => console.log( 'error', err )
    );
  };

  render() {
    return (
      <div>
        <h3>{this.props.wpObject.title ? this.props.wpObject.title : 'WP Live Stats'}</h3>
        {this.state.stats.length > 0 && this.state.stats.map( (site, id) => {
          return (
            <React.Fragment key={id}>
              <h4>{this.state.stats[id].site_name}</h4>
              <p>Posts: {this.state.stats[id].total_posts}</p>
              <p>Pages: {this.state.stats[id].total_pages}</p>
              <p>Users: {this.state.stats[id].total_users}</p>
              <p>Categories: {this.state.stats[id].total_categories}</p>
              <p>Tags: {this.state.stats[id].total_tags}</p>
              <p>Comments: {this.state.stats[id].total_comments}</p>
              <p>Images: {this.state.stats[id].total_images}</p>
              <br></br>
            </React.Fragment>
          )
        })}
      </div>
    );
  }
}

Widget.propTypes = {
  wpObject: PropTypes.object
};
