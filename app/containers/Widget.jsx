import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from '../utils/fetchWP';

export default class Widget extends Component {

  constructor(props) {
    super(props);

    this.state = {
      stats: {}
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
      (json) => this.setState({
        stats: {
          totalPosts: json.total_posts,
          totalPages: json.total_pages,
          totalUsers: json.total_users,
          totalCategories: json.total_categories,
          totalTags: json.total_tags,
          totalComments: json.total_comments,
          totalImages: json.total_images
        }
      }),
      (err) => console.log( 'error', err )
    );
  };

  render() {
    return (
      <div>
        <h1>{this.props.wpObject.title ? this.props.wpObject.title : 'WP Live Stats'}</h1>
        <p>Posts: {this.state.stats.totalPosts}</p>
        <p>Pages: {this.state.stats.totalPages}</p>
        <p>Users: {this.state.stats.totalUsers}</p>
        <p>Categories: {this.state.stats.totalCategories}</p>
        <p>Tags: {this.state.stats.totalTags}</p>
        <p>Comments: {this.state.stats.totalComments}</p>
        <p>Images: {this.state.stats.totalImages}</p>
      </div>
    );
  }
}

Widget.propTypes = {
  wpObject: PropTypes.object
};
