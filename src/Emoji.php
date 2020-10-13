<?php
/**
 * Emoji
 *
 * @since   1.0.0
 * @link    https://github.com/wppunk/emoji/
 * @license GPLv2 or later
 * @package Emoji
 * @author  WPPunk
 */

namespace Emoji;

/**
 * Class Emoji
 *
 * @package Emoji
 */
class Emoji {

	/**
	 * User uuid
	 *
	 * @var \Emoji\UserUuid
	 */
	private $user_uuid;

	/**
	 * DB
	 *
	 * @var \Emoji\DB
	 */
	private $db;

	/**
	 * Emoji constructor.
	 *
	 * @param \Emoji\DB       $db        DB.
	 * @param \Emoji\UserUuid $user_uuid User uuid.
	 */
	public function __construct( $db, $user_uuid ) {
		$this->db        = $db;
		$this->user_uuid = $user_uuid;
	}

	/**
	 * Get post emoji.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array
	 */
	public function get( $post_id ) {
		$emoji = wp_cache_get( 'emoji_' . $post_id );
		if ( $emoji ) {
			return $emoji;
		}

		$allowed_emoji = apply_filters(
			'emoji_view_list',
			[
				'cool'  => 0,
				'happy' => 0,
				'good'  => 0,
				'nerd'  => 0,
				'sad'   => 0,
				'poop'  => 0,
			]
		);
		$emoji         = $this->db->get_emoji( $post_id );

		foreach ( $allowed_emoji as $key => $emotion ) {
			$allowed_emoji[ $key ] = ! empty( $emoji[ $key ] ) ? $emoji[ $key ] : $emotion;
		}
		wp_cache_set( 'emoji_' . $post_id, $allowed_emoji );

		return $allowed_emoji;
	}

	/**
	 * Get user emotion.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string
	 */
	public function user_emotion( $post_id ) {
		$hash    = $this->user_uuid->create( $post_id );
		$emotion = wp_cache_get( 'emotion_' . $hash );
		if ( $emotion ) {
			return $emotion;
		}
		$emotion = $this->db->get_user_emotion( $post_id, $hash );
		wp_cache_set( 'emotion_' . $hash, $emotion );

		return $emotion;
	}

	/**
	 * User vote
	 *
	 * @param int    $post_id      Post ID.
	 * @param string $user_emotion User emotion.
	 *
	 * @return bool
	 */
	public function vote( $post_id, $user_emotion ) {
		do_action( 'emoji_before_vote', $post_id, $user_emotion );
		$emoji = $this->get( $post_id );
		unset( $emoji['poop'] ); // Remove poop emoji because this is a magic emotion.
		if ( ! isset( $emoji[ $user_emotion ] ) ) {
			return false;
		}
		$user_hash = $this->user_uuid->create( $post_id );
		$vote      = $this->db->update_user_emotion( $post_id, $user_hash, $user_emotion );
		wp_cache_delete( 'emoji_' . $post_id );
		wp_cache_delete( 'emotion_' . $user_hash );
		do_action( 'emoji_voted', $post_id, $user_emotion );

		return $vote;
	}

}
