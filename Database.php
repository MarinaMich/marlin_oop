<?php
//пример паттерна Singleton - php класс, в логику работы которого включена проверка на эксклюзивность его создания. Т.е. объект класса, построенный по шаблону синглтона, может быть создан лишь один раз. Все будущие попытки обратиться к его методу или свойству, создав новый объект, будут нейтрализованы логикой его работы и перенаправлены на уже имеющийся экземпляр.
class Database {
	//private - доступен только из методов класса
	//static - определяется не на уровне объекта, а на уровне класса. И выводится через оператор :: и имя класса или self
	//$instance, $instance - свойства класса
	private static $instance = null;
	private $pdo, $stmt, $error = false, $results, $count;

	private function __construct()
	{
		try{
			$this->pdo = new PDO(
				"mysql:host=localhost;dbname=site_marlin;",
    			"root",
    			"mysql");
			
		} catch (PDOException $e)
		{
			echo 'Невозможно установить соединение с базой данных:' . $e->getMessage();
		}
	}
	//метод класса
	//в этом методе созданный объект класса записывается в свойство этого же класса
	public function getInstance()
	{
		if (!isset(self::$instance)){
			self::$instance = new Database;
		}
		return self::$instance;
	}
 	//метод для выполнения запроса на sql
	// string - $sql
	// array - $params
	public function query($sql, $params = [])
	{
		$this->error = false;
		$this->stmt = $this->pdo->prepare($sql);

		if(count($params)){
			$i = 1;
			foreach ($params as $param) {
				$this->stmt->bindValue($i, $param);
				$i++;
			}
		}
		if(!$this->stmt->execute()){
			$this->error = true;
		}
		$this->results = $this->stmt->fetchAll(PDO::FETCH_OBJ);
		$this->count = $this->stmt->rowCount();
		//возвращаем текущий экземпляр класса
		return $this; 
	}
	//для доступа в программе приватных свойств
	public function error()
	{
		return $this->error;
	}

	public function results()
	{
		return $this->results;
	}

	public function count()
	{
		return $this->count;
	}

	//метод получени данных из БД без sql в обработчике 
	public function get($table, $where = [])
	{
		//проверка количества членов массива $where
		if(count($where) === 3){
			//возможные операторы
			$operators = ['=', '>', '<', '>=', '<='];
			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];

			if(in_array($operator, $operators)){
				$sql = "SELECT * FROM {$table} WHERE {$field} {$operator} ?";
				if(!$this->query($sql, [$value])->error()){
					return $this;
				}	
			}
		}
		return false;
	}
}