class Room {
    constructor(data) {
        this._id = data._id || 0;
        this.name = data.name || "";
        this.state = data.state || 1;
    }
}

module.exports = Room;
