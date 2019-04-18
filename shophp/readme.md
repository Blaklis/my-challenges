## ShoPHP
This challenge is a very basic reservation system for some goodies. As the code is provided, we can see fastly that the myCart.php page use the `unserialize()` function on an attacker controled value, but restricts classes that can be unserialized to the Cart one.

We can also see that the Entity class is a custom ORM that provides basic CRUD operations to entities that extends it. While it doesn't seems vulnerable, the PHP bug #77115 (https://bugs.php.net/bug.php?id=77115) allows to create arrays that contains multiple identical keys when using `get_object_vars($this)` on an object that contains two attributes with the same name but with a different visibility.
By trying to unserialize a Cart instance with both a public and a private id attribute, we can see that the `__toArray` call indeed returns an array with two ID keys.

The Entity::save method is the following :

```
public function save() {
   global $db;
   $fields = static::FIELDS;
   $fields_value = $this->__toArray();
   $id = $fields_value['id'];
   if(!is_null($id) && is_numeric($id) && $this->get($id)) {
      foreach($fields_value as $field_name => $field_value) {
         if(!in_array($field_name,$fields) || !is_string($field_value)) { unset($fields_value[$field_name]); continue;}
         $fields_value[$field_name] = $db->real_escape_string($field_value);
      }
      $cond = array();
      unset($fields_value['id']);
      foreach($fields_value as $field_name => $field_value) {
         $cond[] = "`$field_name` = '$field_value'";
      }
      $query = "UPDATE ".static::TABLE_NAME." SET ".implode(",",$cond)." WHERE id = ".intval($id);
      $res = $db->query($query);
   } else {
      $res = false;
   }
   $this->get($this->id);
   return $res;
}
```

As we can see, the code correctly check for each attributes that it's an existing field on the object, and then escapes it if yes, or drop it if not.
However, the escaping part will only escapes one of the two ID attributes that are stored in the `$field_values` array. The other one will stay intact, as it can't be accessed through the `$field_values['id']` notation.
The escaped ID is then removed from the fields to update, as we don't want to update the ID on an update request; this lets the unescaped ID to be used in the query, which obviously leads to a SQL injection.

Here is a serialized payload that triggers the SQL injection on the confirm step; here, as a PoC, a sleep(10) :

O:4:"Cart":3:{s:2:"id";s:1:"1";s:10:"\0Entity\0id";s:25:"' where id=sleep(10) -- -";s:5:"items";s:1:"1";}
