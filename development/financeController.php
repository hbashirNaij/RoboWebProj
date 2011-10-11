<?php
/**
 * This class contains all the function related to the finance system. It is currently a subclass of roboSISAPI so as to keep it logically separate, yet still have easy access to general functions such as getUserID.
 */
class financeController extends roboSISAPI
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	// INPUT FUNCTIONS
	
	/**
	 * This method inserts a new order into the database.
	 * This method should be called only when an order is inputted into the database for the first time. Updating an order that has already been started should call updateOrder.
	 * Check the finance tester for an example of how the $orders and $orderslist arrays should be structured, the examples are too large to reasonably fit here.
	 * $username: the user who is submitting the order
	 */
	public function inputOrder($username, $orders, $orderslist)
	{
		$id = parent::getUserID($username);
		$orders["UserID"] = $id; // saves the front-end from making the api call to get the UserID
		$uniqueID = uniqid("UID"); // generates a unique 16-character string starting with "UID"
		$orders["UniqueID"] = $uniqueID; // adds the uniqueID string to the orders array, with key "UniqueID"
		// inserts the general orders-related info into the OrdersTable
		//print_r($orders);
		$this->_dbConnection->insertIntoTable("OrdersTable", "RoboUsers", "UserID", $id, "UserID", $orders);
		//echo 'yes';
		//return;
		// code to get the orderID that was just created from the insert call
		$resourceid = $this->_dbConnection->selectFromTable("OrdersTable", "UniqueID", $uniqueID);
		$arr = $this->_dbConnection->formatQueryResults($resourceid, "OrderID");
		$orderID = $arr[0];
		// iterates through orderslist and inserts the list of parts and associated info into the orderslist table
		for ($i=0; $i < count($orderslist); $i++)
		{
			$list = $orderslist[$i];
			$list["UniqueEntryID"] = uniqid("UEID");
			$this->_dbConnection->insertIntoTable("OrdersListTable", "OrdersTable", "OrderID", $orderID, "OrderID", $list);
		}
		//echo "success";
	}
	
	/**
	 * Updates an order with new information
	 */
	public function updateOrder($orderID, $orders, $orderslist)
	{
		//$id = parent::getUserID($username);
		// updates the order with given orderID
		$this->_dbConnection->updateTable("OrdersTable", "OrdersTable", "OrderID", $orderID, "OrderID", $orders, "OrderID = $orderID");
		// iterates through orderslist and inserts the list of parts and associated info into the orderslist table
		//echo "win";
		//return;
		for ($i=0; $i < count($orderslist); $i++)
		{
			$list = $orderslist[$i];
			//print_r($list);
			$condition = "UniqueEntryID = " . $list["UniqueEntryID"];
			//print_r($condition);
			$this->_dbConnection->updateTable("OrdersListTable", "OrdersListTable", "UniqueEntryID", $list["UniqueEntryID"], "OrderID", $list, $condition);
		}
		//echo "success";
	}
	
	// OUTPUT FUNCTIONS
	
	/**
	 * $orderID: the id of the order to get
	 */
	public function getOrder($orderID)
	{
		$resourceid = $this->_dbConnection->selectFromTable("OrdersTable", "OrderID", $orderID);
		$order = $this->_dbConnection->formatQuery($resourceid); // gets order with keys being column names
		return $order;
	}
	
	/**
	 * Gets the list of parts associated with the given order
	 */
	public function getOrdersList($orderID)
	{
		$resourceid = $this->_dbConnection->selectFromTable("OrdersListTable", "OrderID", $orderID); // bug: only gives first row with given orderID
		$arr = $this->_dbConnection->formatQuery($resourceid); // custom method built for this purpose
		return $arr;
	}
	
	/**
	 * Returns a 2D array in JSON format of all the past orders the given user has placed, with most recent order on top. First array is orders, second array is orderslists, with sub-arrays being individual orders or lists(arrays) of parts per order.
	 */
	public function getUsersOrders($username)
	{
		$id = parent::getUserID($username);
		$resourceid = $this->_dbConnection->selectFromTableDesc("OrdersTable", "UserID", $id, "NumericDateSubmitted"); // orders in most recently edited/submitted
		$arr = $this->_dbConnection->formatQueryResults($resourceid, "OrderID"); // holds list of all the OrderIDs of the orders that the given user has placed
		$orders = array(); // will be an array of arrays, each contained array being an order
		for ($i=0; $i < count($arr); $i++)
		{
			$orders[$i] = $this->getOrder($arr[$i]); // gets a single order and adds it to orders array
		}
		$lists = array();
		for ($i=0; $i < count($orders); $i++)
		{
			$lists[$i] = $this->getOrdersList($orders[$i][0]["OrderID"]); // gets the list of orderlist entries with given orderID as an array and stores it into one element of the lists array
		}
		$users_orders = array($orders, $lists); // puts into a 2D array
		//$users_orders[] = $this->getOrder($arr[2]); // gets a single order
		//return $orders;
		return json_encode($users_orders);
	}
	
	/**
	 * gets ALL orders in the database in JSON format, with keys as db column names
	 */
	public function getAllOrders()
	{
		$resourceid = $this->_dbConnection->selectFromTable("OrdersTable");
		$orders = $this->_dbConnection->formatQuery($resourceid);
		return json_encode($orders);
	}
	
	/**
	 * Locks the order for processing and notifies admin
	 */
	public function submitToAdmin($orderID)
	{
		
	}
	
	// ADMIN FUNCTIONS
	
	/**
	 * Gets the list of pending orders in JSON
	 */
	public function getPendingOrders()
	{
		$resourceid = $this->_dbConnection->selectFromTable("OrdersTable", "Status", "Pending");
		$orders = $this->_dbConnection->formatQuery($resourceid);
		return json_encode($orders);
	}
	
	/**
	 * Sets the admin approval for an order
	 * orderID: the orderID of the order to update
	 * approved: Either boolean true for approved or false for rejected
	 * comment: an optional text comment
	 */
	public function setApproval($orderID, $approved, $comment = null)
	{
		$status = "";
		if ($approved)
		{
			$approved = 1; // allows to write to DB, since AdminApproved is an int, 1 = true 0 = false
			$status = "Approved";
		}
		else
		{
			$approved = 0;
			$status = "";
		}
		
	}
}

?>