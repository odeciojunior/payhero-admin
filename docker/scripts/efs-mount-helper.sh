#!/bin/bash
# PayHero EFS Mount Helper Script
#
# This script handles mounting of AWS EFS filesystems safely inside Docker containers
# It provides robust error handling and recovery mechanisms

set -e

# Default values
EFS_FILESYSTEM_ID=${EFS_FILESYSTEM_ID:-}
EFS_MOUNT_POINT=${EFS_MOUNT_POINT:-}
EFS_ACCESS_POINT=${EFS_ACCESS_POINT:-}
EFS_MOUNT_ENABLED=${EFS_MOUNT_ENABLED:-true}
EFS_REGION=${AWS_REGION:-us-east-1}
EFS_MOUNT_OPTIONS=${EFS_MOUNT_OPTIONS:-nfsvers=4.1,rsize=1048576,wsize=1048576,hard,timeo=600,retrans=2}

# Function to log messages
log() {
    echo "[$(date "+%Y-%m-%d %H:%M:%S")] [EFS Mount Helper] $1"
}

# Function to log errors
error() {
    echo "[$(date "+%Y-%m-%d %H:%M:%S")] [EFS Mount Helper] ERROR: $1" >&2
}

# Function to check if a directory is already mounted
is_mounted() {
    mount | grep -q " on $1 "
}

# Function to mount EFS filesystem
mount_efs() {
    local mount_point=$1
    local filesystem_id=$2
    local access_point=$3
    local region=$4
    local options=$5

    # Create mount point if it doesn't exist
    if [[ ! -d "$mount_point" ]]; then
        log "Creating mount point directory: $mount_point"
        mkdir -p "$mount_point"
    fi

    # Check if already mounted
    if is_mounted "$mount_point"; then
        log "Directory $mount_point is already mounted"
        return 0
    fi

    # Construct mount command based on whether access point is provided
    local mount_command="mount -t nfs4"
    
    if [[ -n "$access_point" ]]; then
        # Using access point
        log "Mounting EFS filesystem $filesystem_id at $mount_point using access point $access_point"
        mount_command="$mount_command -o tls,$options,accesspoint=$access_point $filesystem_id.efs.$region.amazonaws.com:/ $mount_point"
    else
        # Not using access point
        log "Mounting EFS filesystem $filesystem_id at $mount_point"
        mount_command="$mount_command -o tls,$options $filesystem_id.efs.$region.amazonaws.com:/ $mount_point"
    fi

    # Execute the mount command
    log "Executing: $mount_command"
    eval "$mount_command"
    
    # Verify mount was successful
    if is_mounted "$mount_point"; then
        log "Successfully mounted EFS filesystem at $mount_point"
        return 0
    else
        error "Failed to mount EFS filesystem at $mount_point"
        return 1
    fi
}

# Main function to handle EFS mounts
handle_efs_mount() {
    # Check if EFS mounting is enabled
    if [[ "$EFS_MOUNT_ENABLED" != "true" ]]; then
        log "EFS mounting is disabled by configuration"
        return 0
    fi

    # Validate required parameters
    if [[ -z "$EFS_FILESYSTEM_ID" ]]; then
        error "EFS_FILESYSTEM_ID environment variable is required"
        return 1
    fi

    if [[ -z "$EFS_MOUNT_POINT" ]]; then
        error "EFS_MOUNT_POINT environment variable is required"
        return 1
    fi

    # Mount the EFS filesystem
    mount_efs "$EFS_MOUNT_POINT" "$EFS_FILESYSTEM_ID" "$EFS_ACCESS_POINT" "$EFS_REGION" "$EFS_MOUNT_OPTIONS"
    return $?
}

# Function to set proper permissions on mounted directories
set_permissions() {
    local mount_point=$1
    local uid=${2:-1000}
    local gid=${3:-1000}
    local mode=${4:-755}

    # Check if directory exists and is mounted
    if [[ -d "$mount_point" ]] && is_mounted "$mount_point"; then
        log "Setting permissions on $mount_point (uid=$uid, gid=$gid, mode=$mode)"
        chown -R $uid:$gid "$mount_point"
        chmod -R $mode "$mount_point"
    fi
}

# Execute the main function if script is called directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    handle_efs_mount
fi