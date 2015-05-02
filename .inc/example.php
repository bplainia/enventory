class Example implements component 
{
    function __construct($command = null)
    {
        $this->db = startsql();
        if($command == null)
        {
            $this->makeTable();
        }
        $this->components = "No Components";
    }
    public function title()
    {
        return "Example Class";
    }
    
    public function table ()
    {
        return "<p>Table not implemented</p>";
    }
    
    public function modComponent($id,$data)
    {
        ;
    }
    public function useComponent($id,$data)
    {
        ;
    }
    public function addComponent($data)
    {
        ;
    }
    public function delComponent($id)
    {
        ;
    }
    public function makeTable()
    {
        ;
    }
    public function content()
    {
        echo "<p>This is an example</p>\n";
    }
    public function jscript()
    {
        return"";
    }
    public function form()
    {
        echo "";
    }
}